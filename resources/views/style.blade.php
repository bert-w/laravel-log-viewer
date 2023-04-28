<style>
    #log-viewer .table, #log-viewer .list-group {
        font-size: 85%;
    }

    #log-viewer .table {
        font-family: monospace;
    }

    #log-viewer .table .view-stacktrace {
        cursor: pointer;
        user-select: none;
    }

    #log-viewer .table .view-stacktrace:hover, #log-viewer .table .view-stacktrace:focus {
        background: rgba(0,0,0,0.1);
    }

    #log-viewer .table .stacktrace {
        font-size: 85%;
        word-break: break-all;
        white-space: pre-wrap;
    }

    #log-viewer .table th:not(:last-child), td:not(:last-child) {
        white-space: nowrap;
        width: 1px;
    }

    #log-viewer .table .collapsing {
        -webkit-transition-duration: 0s;
        transition-duration: 0s;
    }

    #log-viewer .list-group {
        max-height: 300px;
        overflow-y: scroll;
    }
    .bi {
        width: 1em;
        height: 1em;
        vertical-align: -.125em;
        fill: currentcolor;
    }
    .mt-4 {
        margin-top: 1.5rem !important;
    }
</style>