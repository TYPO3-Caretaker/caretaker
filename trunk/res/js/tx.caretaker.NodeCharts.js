Ext.namespace('tx','tx.caretaker');

tx.caretaker.NodeCharts = Ext.extend(Ext.TabPanel, {
	
    constructor: function(config) {

		config = Ext.apply({
	    	enableTabScroll : true,
			collapsible     : true,
			collapsed       : true,
			height          : 430,
			stateful        : true,
			stateEvents     : ['expand','collapse','tabchange'],
			stateID         : 'tx.caretaker.NodeCharts',
			title           : "Chart",
			activeTab       : 3,
			items:[
				{
					title:'1 h',
					autoLoad  : config.back_path + 'ajax.php?ajaxID=tx_caretaker::nodegraph&node=' + config.node_id +  '&duration= ' +  (60*60)
				},{
					title:'3 h',
					autoLoad  : config.back_path + 'ajax.php?ajaxID=tx_caretaker::nodegraph&node=' + config.node_id + '&duration= ' +  (60*60*3)
				},{
					title:'12 h',
					autoLoad  : config.back_path + 'ajax.php?ajaxID=tx_caretaker::nodegraph&node=' + config.node_id + '&duration= ' +  (60*60*12)
				},{
					title:'24 h',
					autoLoad  : config.back_path + 'ajax.php?ajaxID=tx_caretaker::nodegraph&node=' + config.node_id + '&duration= ' +  (60*60*24)
				},{
					title:'48 h',
					autoLoad  : config.back_path + 'ajax.php?ajaxID=tx_caretaker::nodegraph&node=' + config.node_id + '&duration= ' +  (60*60*48)
				},{
					title:'7 Days',
					autoLoad  : config.back_path + 'ajax.php?ajaxID=tx_caretaker::nodegraph&node=' + config.node_id + '&duration= ' +  (60*60*24*7)
				},{
					title:'1 Month',
					autoLoad  : config.back_path + 'ajax.php?ajaxID=tx_caretaker::nodegraph&node=' + config.node_id + '&duration= ' +  (60*60*24*31)
				},{
					title:'3 Month',
					autoLoad  : config.back_path + 'ajax.php?ajaxID=tx_caretaker::nodegraph&node=' + config.node_id + '&duration= ' +  (60*60*34*93)
				},{
					title:'6 Month',
					autoLoad  : config.back_path + 'ajax.php?ajaxID=tx_caretaker::nodegraph&node=' + config.node_id + '&duration= ' +  (60*60*34*182)
				},{
					title:'12 Month',
					autoLoad  : config.back_path + 'ajax.php?ajaxID=tx_caretaker::nodegraph&node=' + config.node_id + '&duration= ' +  (60*60*34*365)
				}
			]
		}, config);

		tx.caretaker.NodeCharts.superclass.constructor.call(this, config);
	},

	getState: function() {
		var state = {
			collapsed: this.collapsed,
			activeTab: this.activeTab.id
		};
		return state;
	}
	
});

Ext.reg( 'caretaker-nodecharts', tx.caretaker.NodeCharts );
