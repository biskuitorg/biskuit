<?php $view->script('user-edit', 'system/user:app/bundle/user-edit.js', ['vue']) ?>

<validation-observer id="user-edit" v-slot="{ handleSubmit }" slim>
    <form class="uk-form-horizontal" @submit.prevent="handleSubmit(save)" v-cloak>
        <div class="uk-margin uk-flex uk-flex-between uk-flex-wrap" uk-margin>
            <div uk-margin>
                <h2 class="uk-margin-remove" v-if="user.id">{{ 'Edit User' | trans }}</h2>
                <h2 class="uk-margin-remove" v-else>{{ 'Add User' | trans }}</h2>
            </div>
            <div >
                <a class="uk-button uk-button-default uk-margin-small-right" :href="$url.route('admin/user')">{{ user.id ? 'Close' : 'Cancel' | trans }}</a>
                <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>
            </div>
        </div>

        <ul uk-tab v-show="sections.length >=1">
            <li v-for="section in sections"><a>{{ section.label | trans }}</a></li>
        </ul>

        <ul class="uk-switcher uk-margin">
          <li v-for="section in sections">
            <component :is="section.name" :user="user" :config="config"></component>
          </li>
        </ul>

    </form>
</validation-observer>
