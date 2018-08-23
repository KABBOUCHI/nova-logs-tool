export default {
    getLogs(file = null,current_page = 1, search = null) {

        return window.axios.get( search ? `/nova-vendor/KABBOUCHI/nova-logs-tool/logs?file=${file}&page=${current_page}&search=${search}` : `/nova-vendor/KABBOUCHI/nova-logs-tool/logs?file=${file}&page=${current_page}`)
            .then(response => response.data);
    },
    getDailyLogFiles(file = null,current_page = 1) {
        return window.axios.get(`/nova-vendor/KABBOUCHI/nova-logs-tool/daily-log-files`)
            .then(response => response.data);
    },

}