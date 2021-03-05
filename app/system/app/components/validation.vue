<template>
    <validation-provider :vid="id" :customMessages="errorMessages" :rules="rules" v-slot="{ errors, required, ariaMsg, ariaInput }" slim>
        <div v-if="type !== 'password'" :class="getOption('wrapperClass')">
            <label v-if="label" :for="id" :class="getOption('labelClass')">{{ label | trans }} <span>{{ required ? ' *' : '' }}</span></label>
            <div :class="getOption('innerWrapperClass')" >
                <textarea v-if="tag === 'textarea'" :id="id" class="uk-textarea" :class="getOption('elementClass')" :rows="getOption('textarea.rows')" :name="name" :placeholder="placeholder" v-model="innerValue" v-bind="ariaInput" />
                <template v-else>
                    <a v-if="getOption('icon.type') == 'link'" class="uk-form-icon uk-form-icon-flip" @click.prevent="getOption('icon.callback')"  :uk-tooltip="getOption('icon.label')" :uk-icon="getOption('icon.symbol')"></a>
                    <input :id="id" class="uk-input" :class="getOption('elementClass')" :type="type" :name="name" :placeholder="placeholder" :autocomplete="autocomplete" v-model="innerValue" v-bind="ariaInput" />
                </template>
                <p class="uk-form-help-block uk-text-danger" v-if="errors[0]" v-bind="ariaMsg">{{ errors[0] | trans }}</p>
            </div>
        </div>
        <div v-else-if="type === 'password'" :class="getOption('wrapperClass')">
            <label v-if="label" :for="id" :class="getOption('labelClass')">as{{ label | trans }} <span>{{ required ? ' *' : '' }}</span></label>
            <div :class="getOption('innerWrapperClass')">
                <div class="uk-inline uk-width-1-1">
                    <a class="uk-form-icon uk-form-icon-flip" tabindex="-1" @click.prevent="hidePassword = !hidePassword" :uk-tooltip="hidePassword ? 'Show' : 'Hide' | trans" :uk-icon="hidePassword ? 'unlock' : 'lock'"></a>
                    <input :id="id" class="uk-input" :class="getOption('elementClass')" :type="hidePassword ? 'password' : 'text'" :name="name" :placeholder="placeholder" autocomplete="off" v-model="innerValue" v-bind="ariaInput" />
                </div>
                <p class="uk-form-help-block uk-text-danger" v-if="errors[0]" v-bind="ariaMsg">{{ errors[0] | trans }}</p>
            </div>
        </div>
    </validation-provider>
</template>

<script>
    import { ValidationObserver, ValidationProvider, extend, configure } from 'vee-validate';
    import * as rules from 'vee-validate/dist/rules';

    export default {
        name: 'v-validated-input',
        props: {
            id: { type: String, default: '' },
            name: { type: String, default: '' },
            label: { type: String, default: '' },
            autocomplete: { type: String, default: '' },
            placeholder: { type: String, default: '' },
            tag: {
                type: String,
                default: 'input',
                validator(value) {
                    return [
                        'input',
                        'textarea'
                    ].includes(value);
                }
            },
            type: {
                type: String,
                default: 'text',
                validator(value) {
                    return [
                        'url',
                        'text',
                        'password',
                        'tel',
                        'search',
                        'number',
                        'email',
                    ].includes(value);
                }
            },
            rules: { type: [Object, String], default: '' },
            errorMessages: { type: Object, default: () => { return {} } },
            options: {
                type: Object,
                default: () => ({})
            },
            value: { type: null, default: '' }
        },

        data() {
            return {
                innerValue: '',
                hidePassword: true,
                innerOptions: _.merge({
                    wrapperClass: 'uk-margin',
                    labelClass: 'uk-form-label',
                    innerWrapperClass: 'uk-form-controls',
                    elementClass: 'uk-width-1-1',
                    icon: {
                        type: '',
                        symbol: '',
                        callback: () => {}
                    },
                    textarea: {
                        rows: ''
                    }
                }, this.options)
            };
        },

        created() {
            if (this.value) {
                this.innerValue = this.value;
            }
        },

        watch: {
            innerValue(value) {
                this.$emit("input", value);
            },
            value(val) {
                if (val !== this.innerValue) {
                    this.innerValue = val;
                }
            }
        },

        methods: {
            getOption(key) {
                let keyParts = key ? key.split('.') : [];
                let result = keyParts.length > 0 ? this.innerOptions : '';
                for(let keyPart of keyParts) {
                    result = result.hasOwnProperty(keyPart) ? result[keyPart] : '';
                }
                return _.isFunction(result) == true ? result.call() : result;
            }
        }
    };

    configure({
        bails: true,
        mode: 'eager',
        defaultMessage: (field, params) => { Vue.trans(`validations.${params._rule_}`, params) }
    });

    Object.keys(rules).forEach(rule => {
        extend(rule, rules[rule]);
    });

    Vue.component('validation-observer', (resolve, reject) => { resolve(ValidationObserver); });
    Vue.component('validation-provider', (resolve, reject) => { resolve(ValidationProvider); });
    Vue.component('v-validated-input', (resolve, reject) => { resolve(require('./validation.vue')); });
</script>
