Ext.namespace('tx','tx.caretaker');

	
tx.caretaker.NodeToolbar = Ext.extend(Ext.Toolbar, {

    constructor: function(config) {
		
		config = Ext.apply({
			layout : "toolbar",
			pading : "5",
            items :  [
                    {
                          text    : "Refresh",
						  xtype   : 'splitbutton',
                          icon    : "../res/icons/arrow_refresh_small.png",
                          handler : this.refreschNode,
							scope   : this
                    },
                    {
                          text    : "Actions",
                          xtype   : 'splitbutton',
                          icon    : "../res/icons/arrow_refresh_small.png",
                          menu    : [
                                
                                 {
                                     text    : "Refresh forced",
                                     icon    : "../res/icons/arrow_refresh.png",
                                     handler : this.refreschNodeForced,
             						 scope   : this
                                 },
                                 {
                                     text    : "Acknowledge Problem",
                                     icon    : "../res/icons/wip.png",
                                     handler : this.setAck,
                                     id      : 'tx_caretaker_NodeToolbar_Ack',
                                     disabled  : ( config.node_type == "test" && config.node_state_info == "ACK") ? true:false,
                                     hidden  : ( config.node_type != "test") ? true:false,
             						scope   : this
             	                },
             	                {
                                     text    : "Set Due to Execution",
                                     icon    : "../res/icons/wip.png",
                                     handler : this.setDue,
                                     id      : 'tx_caretaker_NodeToolbar_Due',
                                     disabled  : ( config.node_type == "test" && config.node_state_info == "DUE") ? true:false,
                                     hidden  : ( config.node_type != "test") ? true:false,
             						 scope   : this
             	                },
                                    ]
                    },
                    {
                            text    : "Edit",
							xtype   : 'splitbutton',
                            icon    : "../res/icons/pencil.png",
                            disabled: (config.node_type =='root')?true:false,
                            handler : this.editNode,
							scope   : this
                    },{
                            text    : "Add Child Record",
							xtype   : 'splitbutton',
                            icon    : "../res/icons/add.png",
							menu    : [
								{
									text    : "Add Instancegroup",
				                	id      : 'toolbar-menu-add-instancegroup',
									icon    : "../res/icons/instancegroup.png",
									disabled: true,
									handler : this.addInstancegroup,
									scope   : this
								},{
									text    : "Add Instance",
				                	id      : 'toolbar-menu-add-instance',
									icon    : "../res/icons/instance.png",
									disabled: true,
									handler : this.addInstance,
									scope   : this
								},{
									text    : "Add Testgroup",
									id      : 'toolbar-menu-add-testgroup',
									icon    : "../res/icons/group.png",
									disabled: true,
									handler : this.addTestgroup,
									scope   : this
								},{
									text    : "Add Test",
									id      : 'toolbar-menu-add-test',
									icon    : "../res/icons/test.png",
									disabled: true,
									handler : this.addTest,
									scope   : this
								}
							]

                    },"->",{
                            text    : "Enable",
							xtype   : 'splitbutton',
                            disabled: (config.node_hidden==0 || config.node_type=='root')?true:false,
                            icon    : "../res/icons/lightbulb.png",
                            handler : this.enableNode,
							scope   : this
                    },
                    {
                            text    : "Disable",
							xtype   : 'splitbutton',
                            icon    : "../res/icons/lightbulb_off.png",
                            disabled: (config.node_hidden==1 || config.node_type=='root')?true:false,
                            handler : this.disableNode,
							scope   : this
                    }

            ]
		}, config);

		this.addUrl = config.add_url;

		tx.caretaker.NodeToolbar.superclass.constructor.call(this, config);

			// add test && testgroup
		var addTestComponent = Ext.getCmp('toolbar-menu-add-test');
		var addTestgroupComponent = Ext.getCmp('toolbar-menu-add-testgroup');
		if ( this.node_type == 'instance' || this.node_type == 'testgroup' ) {
			addTestComponent.enable();
			addTestgroupComponent.enable();
		} else {
			addTestComponent.disable();
			addTestgroupComponent.disable();
		}

			// add instance && instancegroup
		var addInstanceComponent = Ext.getCmp('toolbar-menu-add-instance');
		var addInstancegroupComponent = Ext.getCmp('toolbar-menu-add-instancegroup');
		if ( this.node_type == 'root' || this.node_type == 'instancegroup' ) {
			addInstanceComponent.enable();
			addInstancegroupComponent.enable();
		} else {
			addInstanceComponent.disable();
			addInstancegroupComponent.disable();
		}
	},
	
	addTest: function() {
		var url = '';
		var add_record_type = 'tx_caretaker_test';

		if ( this.node_type == 'instance'){
			url = this.addUrl.replace('###NODE_TYPE###', add_record_type);
			url += '&defVals[' + add_record_type + '][instances]=' + this.node_uid ;

		}

		if ( this.node_type == 'testgroup'){
			url = this.addUrl.replace('###NODE_TYPE###', add_record_type);
			url += '&defVals[' + add_record_type + '][groups]=' + this.node_uid ;
		}

		if (url) {
			url += "&returnUrl=" + this.back_url;
			window.location.href = url;
		}

	},

	addTestgroup: function() {
		var url = '';
		var add_record_type = 'tx_caretaker_testgroup';

		if ( this.node_type == 'instance'){
			url = this.addUrl.replace('###NODE_TYPE###', add_record_type);
			url += '&defVals[' + add_record_type + '][instances]=' + this.node_uid ;

		}

		if ( this.node_type == 'testgroup'){
			url = this.addUrl.replace('###NODE_TYPE###', add_record_type);
			url += '&defVals[' + add_record_type + '][parent_group]=' + this.node_uid ;
		}

		if (url) {
			url += "&returnUrl=" + this.back_url;
            window.location.href = url;
		}

	},

	addInstance: function() {
		var url = '';
		var add_record_type = 'tx_caretaker_instance';

		if ( this.node_type == 'instancegroup'){
			url = this.addUrl.replace('###NODE_TYPE###', add_record_type);
			url += '&defVals[' + add_record_type + '][instancegroup]=' + this.node_uid ;
		}

		if ( this.node_type == 'root'){
			url = this.addUrl.replace('###NODE_TYPE###', add_record_type);
		}

		if (url) {
			url += "&returnUrl=" +  this.back_url;
            window.location.href = url;
		}
	},

	addInstancegroup: function() {
		var url = '';
		var add_record_type = 'tx_caretaker_instancegroup';

		if ( this.node_type == 'instancegroup'){
			url = this.addUrl.replace('###NODE_TYPE###', add_record_type);
			url += '&defVals[' + add_record_type + '][parent_group]=' + this.node_uid ;
		}

		if ( this.node_type == 'root'){
			url = this.addUrl.replace('###NODE_TYPE###', add_record_type);
		}

		if (url) {
			url += "&returnUrl=" + this.back_url;
            window.location.href = url;
		}

	},

	editNode : function (){
        if (this.node_type != 'root'){
            var url = this.path_typo3 + 'alt_doc.php?edit[tx_caretaker_' + this.node_type + '][' + this.node_uid + ']=edit&returnUrl=' + this.back_url;
            window.location.href = url;
        } else {
             top.Ext.MessageBox.alert('Sorry', 'The root node cannot be edited!');
        }
    },

    enableNode : function() {
        if (this.node_hidden == 1 && this.node_type != 'root'){
            var url = this.path_typo3 + 'tce_db.php?&data[tx_caretaker_' + this.node_type + '][' + this.node_uid + '][hidden]=0&redirect=' + this.back_url;
			this.refreshNavigationTree(2000);
            this.redirectUrl(url);
        } else {
            top.Ext.MessageBox.alert('Sorry', 'The node is already enabled');
        }
    },

    disableNode : function() {
        if (this.node_hidden == 0 && this.node_type != 'root'){
            var url = this.path_typo3 + 'tce_db.php?&data[tx_caretaker_' + this.node_type + '][' + this.node_uid + '][hidden]=1&redirect=' + this.back_url;
			this.refreshNavigationTree(2000);
            this.redirectUrl(url);
        } else {
            top.Ext.MessageBox.alert('Sorry', 'The node is already hidden');
        }
    },

	refreschNode : function (){
        Ext.Ajax.request({
			url: this.back_path + 'ajax.php',
			success: this.refreschSuccess,
			failure: this.refreschFailure,
			scope  : this,
			params: {
               ajaxID: 'tx_caretaker::noderefresh',
               node:   this.node_id,
               force:  0
			}
        });
    },

    refreschNodeForced : function (){
         Ext.Ajax.request({
           url: this.back_path + 'ajax.php',
		   success: this.refreschSuccess,
           failure: this.refreschFailure,
		   scope  : this,
           params: {
               ajaxID: 'tx_caretaker::noderefresh',
               node:   this.node_id,
               force:  1
            }
        });
    },
    
    setAck : function (){
        Ext.Ajax.request({
			url: this.back_path + 'ajax.php',
			success: this.refreschSuccess,
			failure: this.refreschFailure,
			scope  : this,
			params: {
               ajaxID: 'tx_caretaker::nodeSetAck',
               node:   this.node_id,
               force:  0
			}
        });
    },
    
    setDue : function (){
        Ext.Ajax.request({
			url: this.back_path + 'ajax.php',
			success: this.refreschSuccess,
			failure: this.refreschFailure,
			scope  : this,
			params: {
               ajaxID: 'tx_caretaker::nodeSetDue',
               node:   this.node_id,
               force:  0
			}
        });
    },

    refreschSuccess : function (response, opts){
    	
    	var	result = Ext.decode(response.responseText);
    	
    	var ack = Ext.getCmp( 'tx_caretaker_NodeToolbar_Ack' );
    	var due = Ext.getCmp( 'tx_caretaker_NodeToolbar_Due' );
    	
    	if ( ack && this.node_type == 'test' ){ 
	    	if (result['state_info'] != 'ACK' ){
	    		ack.enable();
	    	} else {
	    		ack.disable();
	    	}
    	}
    	
    	if ( due && this.node_type == 'test' ){ 
	    	if (result['state_info'] != 'DUE' ){
	    		due.enable();
	    	} else {
	    		due.disable();
	    	}
    	}
	    	
        var node_info_panel = Ext.getCmp('node-info');
        node_info_panel.load( this.back_path + 'ajax.php?ajaxID=tx_caretaker::nodeinfo&node=' + this.node_id);
      
        if ( parent.nav_frame && parent.nav_frame.tx_caretaker_updateTreeById ) {
			parent.nav_frame.tx_caretaker_updateTreeById( this.node_id );
		}
        
    },

    refreschFailure : function (response, opts){
        var node_info_panel = Ext.getCmp('node-info');
        node_info_panel.load( this.back_path + 'ajax.php?ajaxID=tx_caretaker::nodeinfo&node=' + this.node_id);
		this.refreshTree();
    },

	redirectUrl : function( url, defer ){
		if (defer){
			call_url =  function(){
				window.location.href = url;
			}
			call_url.defer(defer);
		} else {
			window.location.href = url;
		}
	},
	
	refreshNavigationTree : function ( defer ){
		if (top.content.nav_frame && top.content.nav_frame.tx.caretaker.view){
			var cartaker_tree = top.content.nav_frame.tx.caretaker.view.get('cartaker-tree');
			if (!defer) defer = false;
			cartaker_tree.reloadTreeDeferred(defer);
		}
	},

	showUpdateLog : function(log){
		
		var win = new Ext.Window({
			autoScroll  : true,
			width       : 600,
			height      : 200,
			modal       : true,                                                  // 1
			title       : 'Refresh Node Log',
			plain       : true,
			border      : false,
			resizable   : false,                                                 // 2
			draggable   : false,                                                 // 3
			closable    : false,                                                 // 4
			buttonAlign : 'center',
			items       : {
					xtype : "panel",
					html  : log
				},
			buttons     : [{
					text    : 'OK',
					handler : function() {
					   win.close();
					}
				}]
		});
		win.show();

	}


});

Ext.reg( 'caretaker-nodetoolbar', tx.caretaker.NodeToolbar );


