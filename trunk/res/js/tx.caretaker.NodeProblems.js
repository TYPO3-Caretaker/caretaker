

Ext.namespace('tx','tx.caretaker');

tx.caretaker.NodeProblems = Ext.extend( Ext.grid.GridPanel , {

    constructor: function(config) {
		
		this.json_data_store = new Ext.data.JsonStore({
			root: 'nodeProblems',
			totalProperty: 'totalCount',
			idProperty: 'node_id',
			remoteSort: false,
			fields: [
				'num',
				'title',
				'node_title',
				'node_id',
				'instance_title',
				'instance_id',
				{name: 'timestamp', mapping: 'timestamp', type: 'date', dateFormat: 'timestamp'},
				'stateinfo',
				'stateinfo_ll',
				'message',
				'message_ll',
				'state'
			],
			proxy: new Ext.data.HttpProxy({
				url: config.back_path + 'ajax.php?ajaxID=tx_caretaker::nodeproblems&node=' + config.node_id
			})
		});

		this.column_model = new Ext.grid.ColumnModel({
			defaults: {
				width: 120,
				sortable: true
			},
			columns: [
			{
				header: "Title",
				dataIndex: 'node_title'
			},{
				header: "Instance",
				dataIndex: 'instance_title'
			},{
				header: "Time",
				dataIndex: 'timestamp'
			},{
				header: "State",
				dataIndex: 'stateinfo_ll'
			},{
				header:'Message',
				dataIndex: 'message_ll'
			}]
		});

		this.column_view = new Ext.grid.GridView({
			forceFit:true,
			enableRowBody:true,
			showPreview:true,
			getRowClass: function(record, index) {
				var state = parseInt( record.get('state') );
				switch (state) {
					case 0:
						return 'tx_caretaker_node_logrow tx_caretaker_node_logrow_OK';
					case 1:
						return 'tx_caretaker_node_logrow tx_caretaker_node_logrow_WARNING';
					case 2:
						return 'tx_caretaker_node_logrow tx_caretaker_node_logrow_ERROR';
					default:
						return 'tx_caretaker_node_logrow tx_caretaker_node_logrow_UNDEFINED';
				}
			}
		});
			
		config = Ext.apply({

			collapsed        : true,
			collapsible      : true,
			stateful         : true,
			stateEvents      : ['expand','collapse'],
			stateId          : 'tx.caretaker.NodeProblems',
			title            :'Problems',
			store            : this.json_data_store,
			trackMouseOver   : false,
			disableSelection : true,
			loadMask         : true,
			autoHeight       : true,
			colModel         : this.column_model,
			view             : this.column_view
			
		}, config);

		tx.caretaker.NodeProblems.superclass.constructor.call(this, config);

		if (this.collapsed == false){
			this.json_data_store.load();
		}
		
		this.on('expand', function(){
			this.json_data_store.load();
		}, this);


	},

	getState: function() {
		var state = {
			collapsed: this.collapsed
		};
		return state;
	}

});

Ext.reg( 'caretaker-nodeproblems', tx.caretaker.NodeProblems );