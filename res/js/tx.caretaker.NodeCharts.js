Ext.namespace('tx', 'tx.caretaker');

tx.caretaker.NodeChartsStatefulTabs = Ext.extend(Ext.TabPanel, {
    stateEvents: ['tabchange'],
    getState: function() {
        return {tab: this.getActiveTab().id}
    },
    applyState: function(state) {
        this.setActiveTab(state.tab);
    }
});

tx.caretaker.NodeCharts = Ext.extend(Ext.Panel, {

    constructor: function(config) {

        var tabpanel = new tx.caretaker.NodeChartsStatefulTabs(
            {
                xtype: 'tabpanel',
                id: 'tx-caretaker-panel-nodecharts-charts',
                region: 'south',
                height: 430,
                activeTab: 3,
                items: [
                    {
                        title: '1 h',
                        autoLoad: TYPO3.settings.ajaxUrls['tx_caretaker::nodegraph'] + '&node=' + config.node_id + '&duration= ' + (60 * 60)
                    }, {
                        title: '3 h',
                        autoLoad: TYPO3.settings.ajaxUrls['tx_caretaker::nodegraph'] + '&node=' + config.node_id + '&duration= ' + (60 * 60 * 3)
                    }, {
                        title: '12 h',
                        autoLoad: TYPO3.settings.ajaxUrls['tx_caretaker::nodegraph'] + '&node=' + config.node_id + '&duration= ' + (60 * 60 * 12)
                    }, {
                        title: '24 h',
                        autoLoad: TYPO3.settings.ajaxUrls['tx_caretaker::nodegraph'] + '&node=' + config.node_id + '&duration= ' + (60 * 60 * 24)
                    }, {
                        title: '48 h',
                        autoLoad: TYPO3.settings.ajaxUrls['tx_caretaker::nodegraph'] + '&node=' + config.node_id + '&duration= ' + (60 * 60 * 48)
                    }, {
                        title: '1 Week',
                        autoLoad: TYPO3.settings.ajaxUrls['tx_caretaker::nodegraph'] + '&node=' + config.node_id + '&duration= ' + (60 * 60 * 24 * 7)
                    }, {
                        title: '2 Weeks',
                        autoLoad: TYPO3.settings.ajaxUrls['tx_caretaker::nodegraph'] + '&node=' + config.node_id + '&duration= ' + (60 * 60 * 24 * 14)
                    }, {
                        title: '1 Month',
                        autoLoad: TYPO3.settings.ajaxUrls['tx_caretaker::nodegraph'] + '&node=' + config.node_id + '&duration= ' + (60 * 60 * 24 * 31)
                    }, {
                        title: '3 Months',
                        autoLoad: TYPO3.settings.ajaxUrls['tx_caretaker::nodegraph'] + '&node=' + config.node_id + '&duration= ' + (60 * 60 * 34 * 93)
                    }, {
                        title: '6 Months',
                        autoLoad: TYPO3.settings.ajaxUrls['tx_caretaker::nodegraph'] + '&node=' + config.node_id + '&duration= ' + (60 * 60 * 34 * 182)
                    }, {
                        title: '12 Months',
                        autoLoad: TYPO3.settings.ajaxUrls['tx_caretaker::nodegraph'] + '&node=' + config.node_id + '&duration= ' + (60 * 60 * 34 * 365)
                    }
                ]

            }
        );

        config = Ext.apply({
            iconCls: 'tx-caretaker-panel-nodecharts',
            enableTabScroll: true,
            collapsible: true,
            collapsed: true,
            autoHeight: true,
            stateful: true,
            stateEvents: ['expand', 'collapse'],
            stateID: 'tx.caretaker.NodeCharts',
            id: 'tx-caretaker-panel-nodecharts',
            title: "Charts",
            titleCollapse: true,
            items: [tabpanel]
        }, config);

        tx.caretaker.NodeCharts.superclass.constructor.call(this, config);
    },

    getState: function() {
        var state = {
            collapsed: this.collapsed
        };
        return state;
    }

});

Ext.reg('caretaker-nodecharts', tx.caretaker.NodeCharts);
