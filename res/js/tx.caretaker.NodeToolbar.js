Ext.namespace('tx', 'tx.caretaker');

tx.caretaker.NodeToolbar = Ext.extend(Ext.Toolbar, {

    constructor: function(config) {

        config = Ext.apply({
            layout: "toolbar",
            pading: "5",
            items: [
                {
                    text: "Refresh",
                    xtype: 'splitbutton',
                    icon: "../typo3conf/ext/caretaker/res/icons/arrow_refresh_small.png",
                    handler: this.refreshNode,
                    scope: this
                },
                {
                    text: "Actions",
                    xtype: 'splitbutton',
                    icon: "../typo3conf/ext/caretaker/res/icons/arrow_refresh_small.png",
                    menu: [

                        {
                            text: "Refresh forced",
                            icon: "../typo3conf/ext/caretaker/res/icons/arrow_refresh.png",
                            handler: this.refreshNodeForced,
                            scope: this
                        },
                        {
                            text: "Acknowledge Problem",
                            icon: "../typo3conf/ext/caretaker/res/icons/wip.png",
                            handler: this.setAck,
                            id: 'tx_caretaker_NodeToolbar_Ack',
                            disabled: ( config.node_type == "test" && config.node_state_info == "ACK") ? true : false,
                            hidden: ( config.node_type != "test") ? true : false,
                            scope: this
                        },
                        {
                            text: "Set Due to Execution",
                            icon: "../typo3conf/ext/caretaker/res/icons/wip.png",
                            handler: this.setDue,
                            id: 'tx_caretaker_NodeToolbar_Due',
                            disabled: ( config.node_type == "test" && config.node_state_info == "DUE") ? true : false,
                            hidden: ( config.node_type != "test") ? true : false,
                            scope: this
                        },
                    ]
                },
                {
                    text: "Edit",
                    xtype: 'splitbutton',
                    icon: "../typo3conf/ext/caretaker/res/icons/pencil.png",
                    disabled: (config.node_type == 'root') ? true : false,
                    handler: this.editNode,
                    scope: this
                }, {
                    text: "Add Child Record",
                    xtype: 'splitbutton',
                    icon: "../typo3conf/ext/caretaker/res/icons/add.png",
                    menu: [
                        {
                            text: "Add Instancegroup",
                            id: 'toolbar-menu-add-instancegroup',
                            icon: "../typo3conf/ext/caretaker/res/icons/instancegroup.png",
                            disabled: true,
                            handler: this.addInstancegroup,
                            scope: this
                        }, {
                            text: "Add Instance",
                            id: 'toolbar-menu-add-instance',
                            icon: "../typo3conf/ext/caretaker/res/icons/instance.png",
                            disabled: true,
                            handler: this.addInstance,
                            scope: this
                        }, {
                            text: "Add Testgroup",
                            id: 'toolbar-menu-add-testgroup',
                            icon: "../typo3conf/ext/caretaker/res/icons/group.png",
                            disabled: true,
                            handler: this.addTestgroup,
                            scope: this
                        }, {
                            text: "Add Test",
                            id: 'toolbar-menu-add-test',
                            icon: "../typo3conf/ext/caretaker/res/icons/test.png",
                            disabled: true,
                            handler: this.addTest,
                            scope: this
                        }
                    ]

                }, "->", {
                    text: "Enable",
                    xtype: 'splitbutton',
                    disabled: (config.node_hidden == 0 || config.node_type == 'root') ? true : false,
                    icon: "../typo3conf/ext/caretaker/res/icons/lightbulb.png",
                    handler: this.enableNode,
                    scope: this
                },
                {
                    text: "Disable",
                    xtype: 'splitbutton',
                    icon: "../typo3conf/ext/caretaker/res/icons/lightbulb_off.png",
                    disabled: (config.node_hidden == 1 || config.node_type == 'root') ? true : false,
                    handler: this.disableNode,
                    scope: this
                }

            ]
        }, config);

        this.addUrl = config.add_url;
        this.getModuleUrlUrl = config.getModuleUrlUrl;
        this.storagePid = config.storagePid;

        tx.caretaker.NodeToolbar.superclass.constructor.call(this, config);

        // add test && testgroup
        var addTestComponent = Ext.getCmp('toolbar-menu-add-test');
        var addTestgroupComponent = Ext.getCmp('toolbar-menu-add-testgroup');
        if (this.node_type == 'instance' || this.node_type == 'testgroup') {
            addTestComponent.enable();
            addTestgroupComponent.enable();
        } else {
            addTestComponent.disable();
            addTestgroupComponent.disable();
        }

        // add instance && instancegroup
        var addInstanceComponent = Ext.getCmp('toolbar-menu-add-instance');
        var addInstancegroupComponent = Ext.getCmp('toolbar-menu-add-instancegroup');
        if (this.node_type == 'root' || this.node_type == 'instancegroup') {
            addInstanceComponent.enable();
            addInstancegroupComponent.enable();
        } else {
            addInstanceComponent.disable();
            addInstancegroupComponent.disable();
        }
    },

    addTest: function() {
        Ext.Ajax.request({
            url: this.getModuleUrlUrl,
            params: {
                "tx_caretaker[mode]": 'add',
                "tx_caretaker[parent]": this.node_uid,
                "tx_caretaker[isInInstance]": (this.node_type == 'instance' ? 1 : 0),
                "tx_caretaker[type]": 'test',
                "tx_caretaker[storagePid]": this.storagePid,
                "tx_caretaker[returnUrl]": encodeURIComponent(window.location)
            },
            success: function(response) {
                if (response.status == 200 && response.responseText && response.responseText.length > 0) {
                    window.location.href = response.responseText + "&returnUrl=" + encodeURIComponent(window.location.href);
                } else {
                    // here should be some error handling
                    if (console && console.log == 'function') {
                        console.debug('There was an error while getting the edit module url');
                    }
                    //Ext.Msg.show({
                    //	title: 'Error',
                    //	text: 'An error occurred while getting the edit form',
                    //	icon: Ext.MessageBox.ERROR
                    //});
                }
            },
            scope: this
        });
    },

    addTestgroup: function() {
        Ext.Ajax.request({
            url: this.getModuleUrlUrl,
            params: {
                "tx_caretaker[mode]": 'add',
                "tx_caretaker[parent]": this.node_uid,
                "tx_caretaker[isInInstance]": (this.node_type == 'instance' ? 1 : 0),
                "tx_caretaker[type]": 'testgroup',
                "tx_caretaker[storagePid]": this.storagePid,
                "tx_caretaker[returnUrl]": encodeURIComponent(parent.list_frame.document.location)
            },
            success: function(response) {
                if (response.status == 200 && response.responseText && response.responseText.length > 0) {
                    window.location.href = response.responseText + "&returnUrl=" + encodeURIComponent(window.location.href);
                } else {
                    // here should be some error handling
                    if (console && console.log == 'function') {
                        console.debug('There was an error while getting the edit module url');
                    }
                    //Ext.Msg.show({
                    //	title: 'Error',
                    //	text: 'An error occurred while getting the edit form',
                    //	icon: Ext.MessageBox.ERROR
                    //});
                }
            },
            scope: this
        });
    },

    addInstance: function() {
        Ext.Ajax.request({
            url: this.getModuleUrlUrl,
            params: {
                "tx_caretaker[mode]": 'add',
                "tx_caretaker[parent]": this.node_type == "root" ? "root" : this.node_uid,
                "tx_caretaker[type]": 'instance',
                "tx_caretaker[storagePid]": this.storagePid,
                "tx_caretaker[returnUrl]": encodeURIComponent(parent.list_frame.document.location)
            },
            success: function(response) {
                if (response.status == 200 && response.responseText && response.responseText.length > 0) {
                    window.location.href = response.responseText + "&returnUrl=" + encodeURIComponent(window.location.href);
                } else {
                    // here should be some error handling
                    if (console && console.log == 'function') {
                        console.debug('There was an error while getting the edit module url');
                    }
                    //Ext.Msg.show({
                    //	title: 'Error',
                    //	text: 'An error occurred while getting the edit form',
                    //	icon: Ext.MessageBox.ERROR
                    //});
                }
            },
            scope: this
        });
    },

    addInstancegroup: function() {
        Ext.Ajax.request({
            url: this.getModuleUrlUrl,
            params: {
                "tx_caretaker[mode]": 'add',
                "tx_caretaker[parent]": this.node_type == "root" ? "root" : this.node_uid,
                "tx_caretaker[type]": 'instancegroup',
                "tx_caretaker[storagePid]": this.storagePid,
                "tx_caretaker[returnUrl]": encodeURIComponent(parent.list_frame.document.location)
            },
            success: function(response) {
                if (response.status == 200 && response.responseText && response.responseText.length > 0) {
                    window.location.href = response.responseText + "&returnUrl=" + encodeURIComponent(window.location.href);
                } else {
                    // here should be some error handling
                    if (console && console.log == 'function') {
                        console.debug('There was an error while getting the edit module url');
                    }
                    //Ext.Msg.show({
                    //	title: 'Error',
                    //	text: 'An error occurred while getting the edit form',
                    //	icon: Ext.MessageBox.ERROR
                    //});
                }
            },
            scope: this
        });
    },

    editNode: function() {
        if (this.node_type != 'root') {
            Ext.Ajax.request({
                url: this.getModuleUrlUrl,
                params: {
                    "tx_caretaker[mode]": 'edit',
                    "tx_caretaker[node]": this.node_uid,
                    "tx_caretaker[type]": this.node_type
                },
                success: function(response) {
                    if (response.status == 200 && response.responseText && response.responseText.length > 0) {
                        window.location.href = response.responseText + "&returnUrl=" + encodeURIComponent(window.location.href);
                    } else {
                        // here should be some error handling
                        if (console && console.log == 'function') {
                            console.debug('There was an error while getting the edit module url');
                        }
                        //Ext.Msg.show({
                        //	title: 'Error',
                        //	text: 'An error occurred while getting the edit form',
                        //	icon: Ext.MessageBox.ERROR
                        //});
                    }
                },
                scope: this
            });
        } else {
            top.Ext.MessageBox.alert('Sorry', 'The root node cannot be edited!');
        }
    },

    enableNode: function() {
        if (this.node_hidden == 1 && this.node_type != 'root') {
            Ext.Ajax.request({
                url: this.getModuleUrlUrl,
                params: {
                    "tx_caretaker[mode]": 'unhide',
                    "tx_caretaker[node]": this.node_uid,
                    "tx_caretaker[type]": this.node_type
                },
                success: function(response) {
                    if (response.status == 200 && response.responseText && response.responseText.length > 0) {
                        Ext.Ajax.request({
                            url: response.responseText,
                            success: function() {
                                this.refreshNavigationTree(0);
                                this.redirectUrl(window.location.href);
                            },
                            failure: function() {
                                Ext.Msg.show({
                                    title: 'Failure',
                                    text: 'Could not hide node',
                                    icon: Ext.MessageBox.WARNING
                                });
                            },
                            scope: this
                        });
                    } else {
                        // here should be some error handling
                        if (console && console.log == 'function') {
                            console.debug('There was an error while getting the hide ajax url');
                        }
                        //Ext.Msg.show({
                        //	title: 'Error',
                        //	text: 'An error occurred while getting the edit form',
                        //	icon: Ext.MessageBox.ERROR
                        //});
                    }
                },
                scope: this
            });
        } else {
            top.Ext.MessageBox.alert('Sorry', 'The node is already enabled');
        }
    },

    disableNode: function() {
        if (this.node_hidden == 0 && this.node_type != 'root') {
            Ext.Ajax.request({
                url: this.getModuleUrlUrl,
                params: {
                    "tx_caretaker[mode]": 'hide',
                    "tx_caretaker[node]": this.node_uid,
                    "tx_caretaker[type]": this.node_type
                },
                success: function(response) {
                    if (response.status == 200 && response.responseText && response.responseText.length > 0) {
                        Ext.Ajax.request({
                            url: response.responseText,
                            success: function() {
                                this.refreshNavigationTree(0);
                                this.redirectUrl(window.location.href);
                            },
                            failure: function() {
                                Ext.Msg.show({
                                    title: 'Failure',
                                    text: 'Could not hide node',
                                    icon: Ext.MessageBox.WARNING
                                });
                            },
                            scope: this
                        });
                    } else {
                        // here should be some error handling
                        if (console && console.log == 'function') {
                            console.debug('There was an error while getting the hide ajax url');
                        }
                        //Ext.Msg.show({
                        //	title: 'Error',
                        //	text: 'An error occurred while getting the edit form',
                        //	icon: Ext.MessageBox.ERROR
                        //});
                    }
                },
                scope: this
            });
        } else {
            top.Ext.MessageBox.alert('Sorry', 'The node is already enabled');
        }
    },

    refreshNode: function() {
        Ext.Ajax.request({
            url: TYPO3.settings.ajaxUrls['tx_caretaker::noderefresh'],
            success: this.refreshSuccess,
            failure: this.refreshFailure,
            scope: this,
            params: {
                node: this.node_id,
                force: 0
            }
        });
    },

    refreshNodeForced: function() {
        Ext.Ajax.request({
            url: TYPO3.settings.ajaxUrls['tx_caretaker::noderefresh'],
            success: this.refreshSuccess,
            failure: this.refreshFailure,
            scope: this,
            params: {
                node: this.node_id,
                force: 1
            }
        });
    },

    setAck: function() {
        Ext.Ajax.request({
            url: TYPO3.settings.ajaxUrls['tx_caretaker::nodeSetAck'],
            success: this.refreshSuccess,
            failure: this.refreshFailure,
            scope: this,
            params: {
                node: this.node_id,
                force: 0
            }
        });
    },

    setDue: function() {
        Ext.Ajax.request({
            url: TYPO3.settings.ajaxUrls['tx_caretaker::nodeSetDue'],
            success: this.refreshSuccess,
            failure: this.refreshFailure,
            scope: this,
            params: {
                node: this.node_id,
                force: 0
            }
        });
    },

    refreshSuccess: function(response, opts) {

        var result = Ext.decode(response.responseText);

        var ack = Ext.getCmp('tx_caretaker_NodeToolbar_Ack');
        var due = Ext.getCmp('tx_caretaker_NodeToolbar_Due');

        if (ack && this.node_type == 'test') {
            if (result['state_info'] != 'ACK') {
                ack.enable();
            } else {
                ack.disable();
            }
        }

        if (due && this.node_type == 'test') {
            if (result['state_info'] != 'DUE') {
                due.enable();
            } else {
                due.disable();
            }
        }

        var node_info_panel = Ext.getCmp('node-info');
        node_info_panel.load(TYPO3.settings.ajaxUrls['tx_caretaker::nodeinfo'] + '&node=' + this.node_id);

        if (parent.nav_frame && parent.nav_frame.tx_caretaker_updateTreeById) {
            parent.nav_frame.tx_caretaker_updateTreeById(this.node_id);
        }

    },

    refreshFailure: function(response, opts) {
        var node_info_panel = Ext.getCmp('node-info');
        node_info_panel.load(TYPO3.settings.ajaxUrls['tx_caretaker::nodeinfo'] + '&node=' + this.node_id);
        this.refreshTree();
    },

    redirectUrl: function(url, defer) {
        if (defer) {
            call_url = function() {
                window.location.href = url;
            }
            call_url.defer(defer);
        } else {
            window.location.href = url;
        }
    },

    refreshNavigationTree: function(defer) {
        if (top.frames.navigation && top.frames.navigation.tx.caretaker.view) {
            var cartaker_tree = top.frames.navigation.tx.caretaker.view.get('cartaker-tree');
            if (!defer) {
                defer = false;
            }
            cartaker_tree.reloadTreeDeferred(defer);
        }
    },

    showUpdateLog: function(log) {

        var win = new Ext.Window({
            autoScroll: true,
            width: 600,
            height: 200,
            modal: true,                                                  // 1
            title: 'Refresh Node Log',
            plain: true,
            border: false,
            resizable: false,                                                 // 2
            draggable: false,                                                 // 3
            closable: false,                                                 // 4
            buttonAlign: 'center',
            items: {
                xtype: "panel",
                html: log
            },
            buttons: [
                {
                    text: 'OK',
                    handler: function() {
                        win.close();
                    }
                }
            ]
        });
        win.show();

    }

});

Ext.reg('caretaker-nodetoolbar', tx.caretaker.NodeToolbar);


