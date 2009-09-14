Ext.ns('tx.caretaker');

Ext.apply(tx.caretaker, new Ext.util.Observable());

tx.caretaker.addEvents({
	nodeChanged: true
});
/*
tx.caretaker.on('nodeChanged', function() {
	Ext.Msg.show({
		title: 'Node changed',
		text: 'test'
	});
});
*/