<template>
    <div>
        <div class="uk-card-badge" v-if="!type.disableToolbar">
            <ul class="uk-subnav">
                <li v-show="type.editable !== false && !innerEditing">
                    <a uk-icon="icon: pencil" class="uk-hidden-hover" :title="$trans('Edit')" data-uk-tooltip="{delay: 500}" @click.prevent="edit"></a>
                </li>
                <li v-show="!innerEditing">
                    <a uk-icon="icon: move "class="uk-hidden-hover uk-sortable-handle" :title="$trans('Drag')" data-uk-tooltip="{delay: 500}"></a>
                </li>
                <li v-show="innerEditing">
                    <a uk-icon="icon: trash" :title="$trans('Delete')" data-uk-tooltip="{delay: 500}" @click.prevent="remove" v-confirm="'Delete widget?'"></a>
                </li>
                <li v-show="innerEditing">
                    <a uk-icon="icon: check" :title="$trans('Close')" data-uk-tooltip="{delay: 500}" @click.prevent="save"></a>
                </li>
            </ul>
        </div>
        <component :is="type.component" :widget="widget" :editing="innerEditing"></component>
    </div>
</template>

<script>
    export default {
        props: { 'widget': {}, 'editing': {default: false} },

        data() {
            return {
                innerEditing: false
            }
        },

        created() {
            if(this.editing === true || this.editing === false) {
                this.innerEditing = this.editing;
            }
            this.$options.components = this.$parent.$options.components;
        },

        computed: {
            type() {
                return this.$root.getType(this.widget.type);
            }
        },

        watch: {
            innerEditing(value) {
                this.$emit('editing', value);
            },
            editing(value) {
                if(value !== this.innerEditing) {
                    this.innerEditing = value;
                }
            }
        },

        methods: {
            edit() {
                this.innerEditing = true;
            },

            save() {
                this.$root.save(this.widget);
                this.innerEditing = false;
            },

            remove() {
                this.$root.remove(this.widget);
            }
        }
    };
</script>
