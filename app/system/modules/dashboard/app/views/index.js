import Version from '../../../../../installer/app/lib/version';

import Panel from '../components/widget-panel.vue';
import Feed from '../components/widget-feed.vue';
import Location from '../components/widget-location.vue';
import util from 'uikit-util';
window.Dashboard = {

    el: '#dashboard',

    data() {
        return _.extend({
            editing: {},
            update: {}
        }, window.$data);
    },

    created() {
        const self = this;
        this.Widgets = this.$resource('admin/dashboard{/id}');
        this.widgets = this.widgets.filter((widget, idx) => {
            if (self.getType(widget.type)) {
                widget.idx = widget.idx === undefined ? idx : widget.idx;
                widget.column = widget.column === undefined ? 0 : widget.column;
                return true;
            }
            return false;
        });
        this.checkVersion();
    },

    mounted() {
        const self = this;
        const sortables = util.$$('.uk-sortable[data-column]');

        util.on(sortables, "added moved removed", function(e, sortable, item) {

            switch (e.type) {
                case 'added':
                case 'removed':

                case 'moved':
                    const widgets = self.widgets;
                    const column = util.data(sortable.$el, "column");
                    let data = {};
                    let widget;


                    sortable.items.forEach(function (item,index) {
                        widget = _.find(widgets, ['id', util.data(item, "id")]);
                        widget.column = column;
                        widget.idx = index;
                    });

                    widgets.forEach(function (widget) {
                        data[widget.id] = widget;
                    });

                  self.$http.post('admin/dashboard/savewidgets', { widgets: data });
            }

        });
    },

    computed: {
        columns() {
            let i = 0;
            return _.groupBy(this.widgets, () => i++ % 3);
        },
        hasUpdate() {
            return this.update && Version.compare(this.update.version, this.version, '>');
        }
    },

    methods: {
        getWidgetsForColumn(column) {
            column = parseInt(column || 0, 10);
            return _.sortBy(this.widgets.filter((widget) => widget.column == column), 'idx');
        },

        add(type) {
             const sortables = util.$$('.uk-sortable[data-column]');
             let column = 0;
             sortables.forEach(function (sortable, index) {
               column = (sortable.childElementCount < sortables[0].childElementCount) ? index : column;
             });

             this.Widgets.save({widget: _.merge({type: type.id, column: column, idx: 100}, type.defaults)}).then(function (res) {
                 const { data } = res;
                 this.widgets.push(data);
                 this.editing[data.id] = true;
             });
         },

        save(widget) {
            const data = { widget };
            //this.$trigger('save:dashboardWidget', data);
            this.Widgets.save({ id: widget.id }, data);
        },

        remove(widget) {
            this.Widgets.delete({ id: widget.id }).then(function () {
                this.widgets.splice(_.findIndex(this.widgets, { id: widget.id }), 1);
            });
        },

        getType(id) {
            return _.find(this.getTypes(), ['id', id]);
        },

        getTypes() {
            let types = [];

            _.forIn(this.$options.components, (component, name) => {
                let type = component.type;
                if (type) {
                    type.component = name;
                    types.push(type);
                }
            });
            return types;
        },

        checkVersion() {
            this.$http.get(this.api + '/api/update', { params: { cache: 60 } }).then(function (res) {
                const update = res.data[this.channel == 'nightly' ? 'nightly' : 'latest'];

                if (update) {
                    this.update = update;
                }
            });
        }
    },

    components: {
        panel: Panel,
        feed: Feed,
        location: Location
    }
};

Vue.ready(window.Dashboard);
