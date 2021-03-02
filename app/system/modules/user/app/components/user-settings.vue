<template>
    <div uk-grid>
        <div class="uk-width-2-3@m uk-width-3-4@l">
            <v-validated-input
                id="form-username"
                name="username"
                :rules="{ required: true, min: 3, regex: /^[a-zA-Z0-9._\-]{3,}$/ }"
                label="Username"
                autocomplete="new-username"
                :error-messages="{ required: 'Username cannot be blank.', min: 'Username must be at least 3 charaters long.', regex: 'Username can only contain alphanumeric characters (A-Z, 0-9) and some special characters (._-)' }"
                :options="{ elementClass: 'uk-form-width-large' }"
                v-model="user.username">
            </v-validated-input>
            <v-validated-input
                id="form-name"
                name="name"
                rules="required"
                label="Name"
                autocomplete="new-name"
                :error-messages="{ required: 'Name cannot be blank.' }"
                :options="{ elementClass: 'uk-form-width-large' }"
                v-model="user.name">
            </v-validated-input>

            <v-validated-input
                id="form-email"
                name="email"
                type="email"
                rules="required|email"
                label="Email"
                autocomplete="new-email"
                :error-messages="{ required: 'Email cannot be blank.', email: 'Field must be a valid email address.' }"
                :options="{ elementClass: 'uk-form-width-large uk-input' }"
                v-model.lazy="user.email">
            </v-validated-input>

            <div class="uk-margin">
                <label for="form-password" class="uk-form-label">{{ 'Password' | trans }}</label>
                <div class="uk-form-controls uk-form-controls-text" v-show="user.id && !editingPassword">
                    <a href="#" @click.prevent="editingPassword = true">{{ 'Change password' | trans }}</a>
                </div>
                <div class="uk-form-controls" :class="{'uk-hidden' : (user.id && !editingPassword)}">
                    <div class="uk-form-password  uk-inline">
                        <input id="form-password" autocomplete="new-password" class="uk-input uk-form-width-large" :type="hidePassword ? 'password' : 'text'" v-model="password">
                        <a href="#" class="bk-form-password-toggle" @click.prevent="hidePassword = !hidePassword">{{ hidePassword ? 'Show' : 'Hide' | trans }}</a>
                    </div>
                </div>
            </div>

            <div class="uk-margin">
                <span class="uk-form-label">{{ 'Status' | trans }}</span>
                <p class="uk-form-controls uk-margin-small" v-for="(status, key) in config.statuses">
                    <label><input class="uk-radio" type="radio" v-model="user.status" :value="parseInt(key)" :disabled="config.currentUser == user.id"> {{ status }}</label>
                </p>
            </div>

            <div class="uk-margin">
                <span class="uk-form-label">{{ 'Roles' | trans }}</span>
                <p class="uk-form-controls uk-margin-small" v-for="role in config.roles">
                  <label><input class="uk-checkbox" type="checkbox" :value="role.id" :disabled="role.disabled" v-model="user.roles"> {{ role.name }}</label>
                </p>
            </div>

            <div class="uk-margin">
                <span class="uk-form-label">{{ 'Last login' | trans }}</span>
                <div class="uk-form-controls uk-form-controls-text">
                    <p>{{ $trans('%date%', { date: user.login ? $date(user.login) : $trans('Never') }) }}</p>
                </div>
            </div>

            <div class="uk-margin">
                <span class="uk-form-label">{{ 'Registered since' | trans }}</span>
                <div class="uk-form-controls uk-form-controls-text">
                    {{ user.registered ? $trans('%date%', { date: $date(user.registered) }) : '' }}
                </div>
            </div>
        </div>

        <div class="uk-width-1-3@m uk-width-1-4@l">
            <div class="uk-panel uk-card uk-text-center" v-show="user.name">
                <div class="uk-panel-teaser">
                    <img height="280" width="280" :alt="user.name" v-gravatar="user.email">
                </div>

                <h3 class="uk-panel-tile uk-margin-remove-bottom uk-text-break">{{ user.name }}
                    <i :title="$trans((isNew ? 'New' : config.statuses[user.status]))" :class="{
                        'pk-icon-circle-primary': isNew,
                        'pk-icon-circle-success': user.access && user.status,
                        'pk-icon-circle-danger': !user.status
                    }"></i>
                </h3>

                <div>
                    <a class="uk-text-break" :href="'mailto:'+user.email">{{ user.email }}</a><i uk-icon="icon: check" :title="$trans('Verified email address')" v-show="config.emailVerification && user.data.verified"></i>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        section: {
            label: 'User'
        },

        props: ['user', 'config'],

        data() {
            return {
                password: '',
                hidePassword: true,
                editingPassword: false
            }
        },

        computed: {
            isNew() {
                return !this.user.login && this.user.status;
            }
        },

        methods: {
            userSave(event, params) {
                params.password = this.password;
            }
        },

        events: {
            'user:save': 'userSave'
        }
    };
</script>
