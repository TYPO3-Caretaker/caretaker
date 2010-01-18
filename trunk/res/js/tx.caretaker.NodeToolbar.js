Ext.namespace('tx','tx.caretaker');

	
tx.caretaker.NodeToolbar = Ext.extend(Ext.Toolbar, {

    constructor: function(config) {
		
		config = Ext.apply({
			layout : "toolbar",
            items :  [
                    {
                            text    : "Refresh",
                            icon    : "../res/icons/arrow_refresh_small.png",
                            handler : this.refreschNode,
							scope   : this
                    },
                    {
                            text    : "Refresh forced",
                            icon    : "../res/icons/arrow_refresh.png",
                            handler : this.refreschNodeForced,
							scope   : this
                    },
                    "-",
                    {
                            text    : "Edit",
                            icon    : "../res/icons/pencil.png",
                            disabled: (config.node_type =='root')?true:false,
                            handler : this.editNode,
							scope   : this
                    },
                    "->",
                    {
                            text    : "Enable",
                            disabled: (config.node_hidden==0 || config.node_type=='root')?true:false,
                            icon    : "../res/icons/lightbulb.png",
                            handler : this.enableNode,
							scope   : this
                    },
                    {
                            text    : "Disable",
                            icon    : "../res/icons/lightbulb_off.png",
                            disabled: (config.node_hidden==1 || config.node_type=='root')?true:false,
                            handler : this.disableNode,
							scope   : this
                    }

            ]
		}, config);
		tx.caretaker.NodeToolbar.superclass.constructor.call(this, config);

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

    refreschSuccess : function (response, opts){
        var node_info_panel = Ext.getCmp('node-info');
        node_info_panel.load( this.back_path + 'ajax.php?ajaxID=tx_caretaker::nodeinfo&node=' + this.node_id);
        this.refreshTree();
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


