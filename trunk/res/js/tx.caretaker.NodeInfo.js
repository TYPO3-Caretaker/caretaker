Ext.namespace('tx','tx.caretaker');

tx.caretaker.NodeInfo = Ext.extend(Ext.Panel, {

    constructor: function(config) {
		config = Ext.apply({
			html            : "node info",
			autoHeight      : true,
			autoLoad        : config.back_path + 'ajax.php?ajaxID=tx_caretaker::nodeinfo&node=' + config.node_id
		}, config);

		tx.caretaker.NodeInfo.superclass.constructor.call(this, config);
	}
});

Ext.reg( 'caretaker-nodeinfo', tx.caretaker.NodeInfo );


