Ext.ns('PMS.Storage.Assets');

PMS.Storage.Assets.Measures = function() {
    
    var data = ['шт.', 'л', 'кг', 'м', 'кв.м', 'куб.м'];
    
    return {
        
        getData: function() {
            return data;
        },
        
        getStore: function() {
            return new Ext.data.ArrayStore({
                autoDestroy: true,
                storeId: 'MeasuresStore',
                idIndex: 0,  
                fields: ['name'],
                data: this.getData()
            });
        },
        
        getCombo: function(config) {
            return Ext.apply(new xlib.form.ComboBox({
                typeAhead: true,
                editable: false,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'local',
                selectFirst: true,
                store: this.getData()
            }), config || {});
        }
    };
}();