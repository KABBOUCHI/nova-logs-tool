<template>
    <div>

        <heading class="mb-3"> Logs</heading>
        <div class="flex justify-between">

            <div class="relative h-9 flex items-center mb-6">
                <icon type="search" class="absolute ml-3 text-70"/>

                <input
                        class="appearance-none form-control form-input w-search pl-search"
                        placeholder="Search"
                        type="search"
                        v-model="search"
                        @keydown.stop="performSearch"
                >
            </div>


            <div v-if="!loading && files.length" class="p-3 flex items-center border-b border-50">

                <button
                        v-if="permissions.canDownload"
                        @click.prevent="download"
                        title="Download"
                        class="cursor-pointer text-70 hover:text-primary mr-3"
                >
                    <icon type="download" view-box="0 0 24 24" width="24" height="24"/>
                </button>
                <button
                        v-if="permissions.canDelete"
                        title="Delete"
                        class="cursor-pointer text-70 hover:text-primary mr-3"
                        @click.prevent="openDeleteModal"
                >
                    <icon type="delete"/>
                </button>

                <select
                        class="form-control form-select"
                        v-model="file" @change="changeFile"
                >
                    <option v-for="file in files" v-text="file"></option>
                </select>
            </div>
        </div>
        <div
                class="relative"
                :class="{'overflow-hidden' : loading}"
        >

            <div v-if="loading" class="flex items-center justify-center z-50 p-6" style="min-height: 150px">
                <loader class="text-60"/>
            </div>
            <template v-else>
                <card>
                    <div class="overflow-hidden overflow-x-auto relative">
                        <table v-if="logs.data.length > 0"
                               class="table w-full"
                               cellpadding="0"
                               cellspacing="0"
                        >
                            <thead>
                            <tr>
                                <th class="text-left" style="width: 100px">
                                    <span class="cursor-pointer inline-flex items-center">
                                    Level
                                    </span>
                                </th>
                                <th class="text-left" style="width: 140px">
                                    <span class="cursor-pointer inline-flex items-center">
                                     Created at
                                    </span>
                                </th>
                                <th class="text-left">
                                    <span class="cursor-pointer inline-flex items-center">
                                     Message
                                    </span>
                                </th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody v-for="(log,index) in logs.data">

                            <tr class="hover:bg-blue-lightest">
                                <td>
                                    <span class="whitespace-no-wrap flex flex-col items-center">
                                        <icon-error v-if="log.level === 'error'"></icon-error>
                                        <icon-info v-if="log.level === 'info'"></icon-info>
                                        <icon-warning v-if="log.level === 'warning'"></icon-warning>
                                        <icon-emergency v-if="log.level === 'emergency'"></icon-emergency>
                                        <icon-alert v-if="log.level === 'alert'"></icon-alert>
                                        <icon-critical v-if="log.level === 'critical'"></icon-critical>
                                        <icon-notice v-if="log.level === 'notice'"></icon-notice>
                                        <icon-debug v-if="log.level === 'debug'"></icon-debug>
                                        <span>{{ log.level }}</span>
                                    </span>
                                </td>
                                <td>
                                    <span class="whitespace-no-wrap">{{ log.date }}</span>
                                </td>
                                <td>
                                    <span class="text-sm text-grey-darker"> {{ log.text }}</span>
                                </td>

                                <td class="td-fit text-right pr-6">
                                    <span @click="viewLog(log)">
                                      <icon type="view" width="22" height="18" view-box="0 0 22 16"/>
                                    </span>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>

                    <div v-if="!logs.data.length" class="flex justify-center items-center px-6 py-8">
                        <div class="text-center">

                            <icon type="search" class="mb-3" width="50" height="50" style="color: #A8B9C5"></icon>

                            <h3 class="text-base text-80 font-normal mb-6">
                                No Logs.
                            </h3>
                        </div>
                    </div>

                    <div class="bg-20 rounded-b">
                        <nav v-if="logs.data.length > 0" class="flex">
                            <!-- Previous Link -->
                            <button
                                    :disabled="!hasPreviousPages"
                                    class="btn btn-link py-3 px-4"
                                    :class="{
                    'text-primary dim': hasPreviousPages,
                    'text-80 opacity-50': !hasPreviousPages,
                }"
                                    rel="prev"
                                    @click.prevent="selectPreviousPage()"
                                    dusk="previous"
                            >
                                {{__('Previous')}}
                            </button>

                            <!-- Next Link -->
                            <button
                                    :disabled="!hasMorePages"
                                    class="ml-auto btn btn-link py-3 px-4"
                                    :class="{
                    'text-primary dim': hasMorePages,
                    'text-80 opacity-50': !hasMorePages,
                }"
                                    rel="next"
                                    @click.prevent="selectNextPage()"
                                    dusk="next"
                            >
                                {{__('Next')}}
                            </button>
                        </nav>
                    </div>
                </card>
            </template>
        </div>

        <transition name="modal" v-if="showLog" @click.self="showLog = null">
            <div class="pin absolute flex items-center justify-center bg-modal" style="z-index:100">
                <div class="bg-white p-4 w-full h-full text-center overflow-y-scroll flex flex-col relative">
                    <div class="mb-4 text-grey-darker">
                        <span class="whitespace-no-wrap flex flex-col items-center">
                                        <icon-error width="100px" v-if="showLog.level === 'error'"></icon-error>
                                        <icon-info width="100px" v-if="showLog.level === 'info'"></icon-info>
                                        <icon-warning width="100px" v-if="showLog.level === 'warning'"></icon-warning>
                                        <icon-emergency width="100px"
                                                        v-if="showLog.level === 'emergency'"></icon-emergency>
                                        <icon-alert width="100px" v-if="showLog.level === 'alert'"></icon-alert>
                                        <icon-critical v-if="showLog.level === 'critical'"></icon-critical>
                                        <icon-notice width="100px" v-if="showLog.level === 'notice'"></icon-notice>
                                        <icon-debug width="100px" v-if="showLog.level === 'debug'"></icon-debug>
                                    <span class="mt-3">{{ showLog.level.toUpperCase() }}</span>
                                    <span class="mt-3">{{ showLog.date }}</span>
                                    </span>
                    </div>
                    <div class="mb-4 flex-1">
                        <pre class="w-full text-left"><code class="language-bash"
                                                            style="white-space: pre-wrap"
                                                            ref="outputCodeMessage"
                                                            v-text="'[message]\n' + showLog.text"></code></pre>
                        <pre class="w-full text-left"><code class="language-bash"
                                                            style="white-space: pre-wrap"
                                                            ref="outputCodeStack"
                                                            v-text="showLog.stack"></code>
                        </pre>

                    </div>
                    <div class="pin-r pin-t absolute p-2">
                        <button class="flex-no-shrink text-info py-2 px-4 rounded" @click="showLog = null">
                            OK
                        </button>
                    </div>
                    <div class="">
                        <button class="flex-no-shrink text-info py-2 px-4 rounded" @click="showLog = null">
                            OK
                        </button>
                    </div>
                </div>
            </div>
        </transition>

        <portal to="modals">
            <transition name="fade">
                <delete-resource-modal
                        v-if="deleteModalOpen"
                        @confirm="confirmDelete"
                        @close="closeDeleteModal"
                        mode="delete"
                >
                    <div class="p-8">
                        <heading :level="2" class="mb-6">
                            Delete Log file
                        </heading>
                        <p class="text-80 leading-normal">
                            Are you sure you want to delete this '{{this.file}}' file?
                        </p>
                    </div>
                </delete-resource-modal>
            </transition>
        </portal>
    </div>
</template>

<style src="prismjs/themes/prism.css"/>

<script>
    import api from '../api';
    import IconError from './icons/IconError';
    import IconInfo from './icons/IconInfo';
    import IconWarning from './icons/IconWarning';
    import IconEmergency from './icons/IconEmergency';
    import IconAlert from './icons/IconAlert';
    import IconCritical from './icons/IconCritical';
    import IconNotice from './icons/IconNotice';
    import IconDebug from './icons/IconDebug';


    import Prism from 'prismjs/components/prism-core'
    import 'prismjs/components/prism-bash'

    export default {
        data() {
            return {
                deleteModalOpen: false,
                search: null,
                loading: true,
                file: 'laravel.log',
                files: [],
                logs: {
                    total: 0,
                    per_page: 2,
                    from: 1,
                    to: 0,
                    data: false,
                    current_page: 1
                },
                showLog: null,
                permissions: {}
            }
        },
        components: {IconError, IconInfo, IconWarning, IconEmergency, IconAlert, IconCritical, IconNotice, IconDebug},
        mounted() {
        },
        async created() {
            document.addEventListener('keydown', this.handleKeydown);
            await this.getLogsPermissions();
            await this.getDailyLogFiles();
            await this.getLogs();
        },
        computed: {
            /**
             * Determine if prior pages are available.
             */
            hasPreviousPages: function () {
                return Boolean(this.logs && this.logs.prev_page_url)
            },

            /**
             * Determine if more pages are available.
             */
            hasMorePages: function () {
                return Boolean(this.logs && this.logs.next_page_url)
            },
        },
        methods: {
            handleKeydown(e) {
                if (e.code === 'Escape') {
                    this.showLog = null;
                }
            },
            download() {
                window.open(`/nova-vendor/KABBOUCHI/logs-tool/logs/${this.file}?time=${new Date().getTime()}`, '_parent');
            },
            getLogsPermissions() {
                return api.getLogsPermissions().then(permissions => {
                    this.permissions = permissions;
                });

            },
            getDailyLogFiles() {
                return api.getDailyLogFiles().then(files => {
                    this.files = files;

                    if (files.length) {
                        this.file = files[0];
                    }
                });

            },
            getLogs(page = 1) {
                this.loading = true;

                return api.getLogs(this.file, page, this.search).then(logs => {
                    this.logs = logs;

                    this.loading = false;

                });
            },
            changeFile() {
                this.search = null;
                this.getLogs()
            },
            selectPreviousPage() {
                this.getLogs(this.logs.current_page - 1);

            },
            selectNextPage() {
                this.getLogs(this.logs.current_page + 1);
            },
            performSearch() {
                this.$nextTick(() => {
                    this.getLogs()
                })
            },
            viewLog(log) {
                this.showLog = log;
                this.$nextTick(() => {
                    Prism.highlightElement(this.$refs.outputCodeMessage)
                    Prism.highlightElement(this.$refs.outputCodeStack)
                })
            },
            openDeleteModal() {
                this.deleteModalOpen = true;
            },
            closeDeleteModal() {
                this.deleteModalOpen = false;
            },
            async confirmDelete() {
                this.deleteModalOpen = false;
                await api.deleteFile(this.file);

                await this.getDailyLogFiles();
                await this.getLogs();
            },
        }
    }
</script>

<style>
</style>
