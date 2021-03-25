<template>
    <div>
        <form class="pk-panel-teaser uk-form uk-form-stacked" v-if="editing">

            <div class="uk-margin">
                <span class="uk-form-label">{{ 'User Type' | trans }}</span>

                <div class="uk-form-controls uk-form-controls-text">
                    <p class="uk-form-controls-condensed">
                        <label><input type="radio" value="login" v-model="widget.show"> {{ 'Logged in' | trans }}</label>
                    </p>

                    <p class="uk-form-controls-condensed">
                        <label><input type="radio" value="registered" v-model="widget.show"> {{ 'Last registered' | trans }}</label>
                    </p>
                </div>
            </div>

            <div class="uk-margin">
                <span class="uk-form-label">{{ 'Display' | trans }}</span>

                <div class="uk-form-controls uk-form-controls-text">
                    <p class="uk-form-controls-condensed">
                        <label><input type="radio" value="thumbnail" v-model="widget.display"> {{ 'Thumbnail' | trans }}</label>
                    </p>

                    <p class="uk-form-controls-condensed">
                        <label><input type="radio" value="list" v-model="widget.display"> {{ 'List' | trans }}</label>
                    </p>
                </div>
            </div>

            <div class="uk-margin">
                <span class="uk-form-label">{{ 'Total Users' | trans }}</span>

                <div class="uk-form-controls uk-form-controls-text">
                    <p class="uk-form-controls-condensed">
                        <label><input type="radio" value="1" v-model="widget.total"> {{ 'Show' | trans }}</label>
                    </p>

                    <p class="uk-form-controls-condensed">
                        <label><input type="radio" value="" v-model="widget.total"> {{ 'Hide' | trans }}</label>
                    </p>
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label" for="form-user-number">{{ 'Number of Users' | trans }}</label>

                <div class="uk-form-controls">
                    <select id="form-user-number" class="uk-width-1-1" v-model.number="widget.count">
                        <option value="6">6</option>
                        <option value="12">12</option>
                        <option value="18">18</option>
                        <option value="24">24</option>
                    </select>
                </div>
            </div>

        </form>

        <div class="uk-text-lead" v-if="widget.total">{{ userscount }}

          <h3 class="uk-text-large uk-text-lighter" v-if="widget.show == 'registered' && widget.total">{{ '{0} Registered Users|{1} Registered User|]1,Inf[ Registered Users' | transChoice(userscount) }}</h3>

          <h3 class="uk-text-large uk-text-lighter" v-if="widget.show != 'registered' && widget.total">{{ '{0} Logged in Users|{1} Logged in User|]1,Inf[ Logged in Users' | transChoice(userscount) }}</h3>

          <h3 class="uk-text-large uk-text-lighter" v-if="widget.show == 'registered' && !widget.total">{{ 'Latest registered Users' | trans }}</h3>

          <h3 class="uk-text-large uk-text-lighter" v-if="widget.show != 'registered' && !widget.total">{{ 'Latest logged in Users' | trans }}</h3>

        </div>
        <ul v-if="users.length && widget.display == 'thumbnail'" data-user class="uk-grid uk-grid-small uk-child-width-1-6@s uk-child-width-1-4@m uk-child-width-1-6@xl" v-stack-margin="users">
            <li v-for="user in users">
                <a :href="$url.route('admin/user/edit', {id: user.id})" :title="user.username">
                    <img class="uk-border-rounded" width="100" height="100" :alt="user.name" v-gravatar="user.email">
                </a>
            </li>
        </ul>

        <ul v-if="users.length && widget.display == 'list'" data-user class="uk-list uk-list-line">
            <li class="uk-flex uk-flex-middle" v-for="user in users">
                <img class="uk-border-circle uk-margin-right" width="40" height="40" :alt="user.name" v-gravatar="user.email">

                <div class="uk-flex-item-1 uk-text-truncate">
                    <a :href="$url.route('admin/user/edit', {id: user.id})" :title="user.name">{{ user.username }}</a>
                    <br><a class="uk-link-muted" :href="`mailto:${user.email}`">{{ user.email }}</a>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
    const UserWidget = {
        type: {
            id: 'user',
            label: 'User',
            description: () => {},
            defaults: {
                show: 'login',
                display: 'thumbnail',
                total: true,
                count: 12
            }
        },

        props: ['widget', 'editing'],

        data() {
            return {
                users: [],
                userscount: null
            }
        },

        watch: {
            'widget.show': {
                handler: 'load',
                immediate: true
            },

            'widget.count': 'load'
        },

        methods: {
            load() {
                let query;
                if (this.widget.show === 'registered') {
                    query = {
                        params: {
                            filter: {order: 'registered DESC'}
                        }
                    }
                } else {
                    query = {
                        params: {
                            filter: {access: 300, order: 'login DESC'}
                        }
                    }
                }
                this.$http.get('api/user/count', query).then(function (res) {
                    this.userscount = res.data.count;
                });

                query.params.limit = this.widget.count;
                this.$http.get('api/user{/id}', query).then(function (res) {
                    this.users = res.data.users.slice(0, this.widget.count);
                });
            }
        }
    };

    export default UserWidget;

    window.Dashboard.components['user'] = UserWidget;
</script>
