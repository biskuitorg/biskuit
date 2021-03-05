<?php $view->script('user-index', 'system/user:app/bundle/user-index.js', 'vue') ?>

<div id="users" v-cloak>
    <div class="uk-margin uk-flex uk-flex-between uk-flex-wrap">
        <div class="uk-flex uk-flex-middle uk-flex-wrap">
            <h2 class="uk-margin-remove" v-if="!selected.length">{{ '{0} %count% Users|{1} %count% User|]1,Inf[ %count% Users' | transChoice(count, {count:count}) }}</h2>

            <template v-else>
                <h2 class="uk-margin-remove">{{ '{1} %count% User selected|]1,Inf[ %count% Users selected' | transChoice(selected.length, {count:selected.length}) }}</h2>
                <div class="uk-margin-left">
                    <ul class="uk-subnav pk-subnav-icon">
                        <li><a class="bk-icon-check pk-icon-hover" :title="$trans('Activate')" data-uk-tooltip="{delay: 500}" @click="status(1)"></a></li>
                        <li><a class="bk-icon-block pk-icon-hover" :title="$trans('Block')" data-uk-tooltip="{delay: 500}" @click="status(0)"></a></li>
                        <li><a class="bk-icon-delete pk-icon-hover" :title="$trans('Delete')" data-uk-tooltip="{delay: 500}" @click.prevent="remove" v-confirm="'Delete users?'"></a></li>
                    </ul>
                </div>
            </template>

            <hr class="uk-divider-vertical bk-divider-vertical" />

            <form class="uk-search uk-search-navbar">
              <span uk-search-icon></span>
              <input class="uk-search-input" type="search" v-model="searchString">
            </form>
        </div>
        <div>
            <a class="uk-button uk-button-primary" :href="$url.route('admin/user/edit')">{{ 'Add User' | trans }}</a>
        </div>
    </div>

    <div class="uk-overflow-auto">
        <table class="uk-table uk-table-hover uk-table-middle">
            <thead>
                <tr>
                    <th class="bk-table-width-minimum"><input type="checkbox" v-check-all:users.number="{ watchedElementsSelector: 'input[name=id]', statusStorageSelector: 'selected' }"></th>
                    <th colspan="2" v-order:username="config.filter.order">
                        {{ 'User' | trans }}
                    </th>
                    <th class="bk-table-width-100 uk-text-center">
                        <input-filter :title="$trans('Status')" :options="statuses" v-model="config.filter.status"></input-filter>
                    </th>
                    <th class="bk-table-width-200" v-order:email="config.filter.order">
                        {{ 'Email' | trans }}
                    </th>
                    <th class="bk-table-width-100">
                        <input-filter :title="$trans('Roles')" :options="roles" v-model="config.filter.role"></input-filter>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="check-item" v-for="user in users" :class="{'uk-active': active(user)}" :id="'user_' + user.id">
                    <td><input type="checkbox" name="id" :value="user.id" v-model="selected"></td>
                    <td class="bk-table-width-minimum">
                        <img class="uk-img-preserve uk-border-circle" width="40" height="40" :alt="user.name" v-gravatar="user.email">
                    </td>
                    <td class="uk-text-nowrap">
                        <a :href="$url.route('admin/user/edit', { id: user.id })">{{ user.username }}</a>
                        <div class="uk-text-muted">{{ user.name }}</div>
                    </td>
                    <td class="uk-text-center">
                        <a href="#" :title="user.statusText" :class="{
                            'bk-icon-circle-success': user.login && user.status,
                            'bk-icon-circle-danger': !user.status,
                            'bk-icon-circle-primary': user.status
                        }" @click="toggleStatus(user)"></a>
                    </td>
                    <td>
                        <a :href="'mailto:'+user.email">{{ user.email }}</a> <i class="uk-icon-check" :title="$trans('Verified Email Address')" v-if="showVerified(user)"></i>
                    </td>
                    <td>
                        {{ showRoles(user) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <h3 class="uk-h1 uk-text-muted uk-text-center" v-show="users && !users.length">{{ 'No user found.' | trans }}</h3>
    <v-pagination :pages="pages" v-model="config.page" v-show="pages > 1 || config.page > 0"></v-pagination>
</div>
