<template>
    <div class="text-xs">
        <Head :title="__('Logs')" />

        <Heading class="mb-3"> {{ __('Logs') }}</Heading>

        <div class="flex items-center justify-between mb-6">
            <div class="relative h-9 w-full md:w-1/3 md:flex-shrink-0">
                <Icon
                    type="search"
                    width="20"
                    class="absolute ml-2 text-gray-400"
                    :style="{ top: '4px' }"
                />

                <RoundInput
                    class="appearance-none g-white dark:bg-gray-800 shadow rounded-full h-8 w-full dark:focus:bg-gray-800"
                    :placeholder="__('Search')"
                    type="search"
                    @input="performSearch"
                    spellcheck="false"
                    :aria-label="__('Search')"
                    @keydown.stop="performSearch"
                />
            </div>

            <div v-if="!loading && files.length" class="flex items-center">
                <button
                    v-if="permissions.canDownload"
                    @click.prevent="download"
                    :title="__('Download')"
                    class="toolbar-button px-2"
                    v-tooltip.click="__('Download')"
                >
                    <Icon type="download" />
                </button>

                <button
                    v-if="permissions.canDelete"
                    :title="__('Delete')"
                    class="toolbar-button px-2"
                    @click.stop="openDeleteModal"
                    v-tooltip.click="__('Delete')"
                >
                    <Icon type="trash" />
                </button>

                <DeleteResourceModal
                    :show="deleteModalOpen"
                    @close="closeDeleteModal"
                    @confirm="confirmDelete"
                    mode="delete"
                >
                    <slot>
                        <ModalHeader v-text="__('Delete log file')" />
                        <ModalContent>
                            <p class="leading-normal">
                                {{
                                    __("Are you sure you want to delete this ':fileName' file?", {
                                        fileName: this.file
                                    })
                                }}
                            </p>
                        </ModalContent>
                    </slot>
                </DeleteResourceModal>

                <select class="ml-2 form-control form-select form-select-bordered" v-model="file" @change="changeFile">
                    <option v-for="file in files" v-text="file"></option>
                </select>
            </div>
        </div>

        <Card>
            <LoadingView :loading="loading">
                <div v-if="logs.data.length > 0">
                    <div class="overflow-hidden overflow-x-auto relative">
                        <table class="w-full table-default">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="uppercase text-xxs text-gray-500 tracking-wide pl-5 pr-2 py-2" style="width: 100px">
                                        {{ __('Level') }}
                                    </th>
                                    <th class="uppercase text-xxs text-gray-500 tracking-wide pl-5 pr-2 py-2" style="width: 140px">
                                        {{ __('Created at') }}
                                    </th>
                                    <th class="uppercase text-xxs text-gray-500 tracking-wide pl-5 pr-2 py-2">
                                        {{ __('Message') }}
                                    </th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr
                                    v-for="(log, index) in logs.data"
                                    :key="index"
                                    class="group"
                                    @click.stop="viewLog(log)"
                                >
                                    <td
                                        class="px-6 py-2 border-t border-gray-100 dark:border-gray-700 dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900 cursor-pointer"
                                    >
                                        <div class="flex flex-col items-center">
                                            <component :is="`icon-${log.level}`"></component>
                                            <span class="mt-2">{{ log.level }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-2 border-t border-gray-100 dark:border-gray-700 dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900 cursor-pointer">
                                        {{ log.date }}
                                    </td>
                                    <td class="px-6 py-2 border-t border-gray-100 dark:border-gray-700 dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900 cursor-pointer">
                                        {{ log.text }}
                                    </td>
                                    <td class="px-6 py-2 border-t border-gray-100 dark:border-gray-700 dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900 cursor-pointer">
                                        <button
                                            class="toolbar-button px-2"
                                            @click.prevent="viewLog(log)"
                                            v-tooltip.click="__('View')"
                                        >
                                            <Icon type="eye"/>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t dark:border-gray-700">
                        <nav v-if="logs.data.length > 0" class="flex">
                            <!-- Previous Link -->
                            <button
                                :disabled="!hasPreviousPages"
                                class="btn btn-link py-3 px-4 font-bold"
                                :class="hasPreviousPages ? 'text-primary-500 hover:text-primary-400' : 'text-gray-300 dark:text-gray-600' "
                                rel="prev"
                                @click.prevent="selectPreviousPage()"
                                dusk="previous"
                            >
                                {{ __('Previous') }}
                            </button>

                            <!-- Next Link -->
                            <button
                                :disabled="!hasMorePages"
                                class="ml-auto btn btn-link py-3 px-4 font-bold"
                                :class="hasMorePages ? 'text-primary-500 hover:text-primary-400' : 'text-gray-300 dark:text-gray-600' "
                                rel="next"
                                @click.prevent="selectNextPage()"
                                dusk="next"
                            >
                                {{ __('Next') }}
                            </button>
                        </nav>
                    </div>

                </div>
                <div v-else class="w-full flex flex-col items-center justify-center min-h-40">
                    <Icon type="search" class="h-14 w-14" />
                    <span>No Logs</span>
                </div>
            </LoadingView>
        </Card>

        <Modal
            :show="showLog"
            @close="showLog = null"
            @close-via-escape="showLog = null"
        >
            <div class="bg-white rounded-md dark:bg-gray-800">
                <slot>
                    <ModalContent>
                        <div class="flex">
                            <button class="ml-auto px-4 py-2 font-bold text-gray-400 hover:text-gray-300 dark:text-gray-500 dark:hover:text-gray-400" type="button" @click.prevent="showLog = null">
                                <Icon type="x" />
                            </button>
                        </div>
                        <div class="flex flex-col items-center">
                            <component :is="`icon-${showLog.level}`" width="100px"></component>
                            <span class="mt-2 uppercase">{{ showLog.level }}</span>
                            <span class="mt-2">{{ showLog.date }}</span>
                        </div>
                        <div>
                        <pre class="text-left dark:bg-gray-700"><code class="language-bash whitespace-pre-wrap" ref="outputCodeMessage" v-text="'[message]\n' + showLog.text" /></pre>
                            <pre class="text-left dark:bg-gray-700"><code class="language-bash whitespace-pre-wrap" ref="outputCodeStack" v-text="showLog.stack" /></pre>
                        </div>
                    </ModalContent>
                    <ModalFooter>
                        <button class="ml-auto px-4 py-2 font-bold text-gray-400 hover:text-gray-300 dark:text-gray-500 dark:hover:text-gray-400" type="button" @click.prevent="showLog = null">
                            Close
                        </button>
                    </ModalFooter>
                </slot>
            </div>
        </Modal>

    </div>
</template>

<style src="prismjs/themes/prism.css" />

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

import Prism from 'prismjs/components/prism-core';
import 'prismjs/components/prism-bash';
import {Tab} from "@headlessui/vue";

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
        };
    },
    components: {
        Tab,
        IconError,
        IconInfo,
        IconWarning,
        IconEmergency,
        IconAlert,
        IconCritical,
        IconNotice,
        IconDebug
    },

    mounted() {},

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
        hasPreviousPages: function() {
            return Boolean(this.logs && this.logs.prev_page_url);
        },

        /**
         * Determine if more pages are available.
         */
        hasMorePages: function() {
            return Boolean(this.logs && this.logs.next_page_url);
        }
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
            this.getLogs();
        },

        selectPreviousPage() {
            this.getLogs(this.logs.current_page - 1);
        },

        selectNextPage() {
            this.getLogs(this.logs.current_page + 1);
        },

        performSearch(event) {
            if (event) {
                this.search = event.target.value
            }

            this.$nextTick(() => {
                this.getLogs();
            });
        },

        viewLog(log) {
            this.showLog = log;
            this.$nextTick(() => {
                Prism.highlightElement(this.$refs.outputCodeMessage);
                Prism.highlightElement(this.$refs.outputCodeStack);
            });
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
};
</script>

<style></style>
