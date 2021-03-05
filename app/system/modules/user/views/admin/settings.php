<?php $view->script('settings', 'system/user:app/bundle/settings.js', ['vue', 'input-link']) ?>

<div id="settings" class="uk-form-horizontal" v-cloak>

    <div class="uk-margin uk-flex uk-flex-between uk-flex-wrap" >
        <div >
            <h2 class="uk-margin-remove">{{ 'Settings' | trans }}</h2>
        </div>
        <div >
            <button class="uk-button uk-button-primary" @click.prevent="save">{{ 'Save' | trans }}</button>
        </div>
    </div>

    <div class="uk-margin">
        <span class="uk-form-label">{{ 'Registration' | trans }}</span>
            <p class="uk-form-controls uk-margin-small">
                <label><input class="uk-radio" class="uk-radio" type="radio" v-model="config.registration" value="admin"> {{ 'Disabled' | trans }}</label>
            </p>
            <p class="uk-form-controls uk-margin-small">
                <label><input class="uk-radio" type="radio" v-model="config.registration" value="guest"> {{ 'Enabled' | trans }}</label>
            </p>
            <p class="uk-form-controls uk-margin-small">
                <label><input class="uk-radio" type="radio" v-model="config.registration" value="approval"> {{ 'Enabled, but approval is required.' | trans }}</label>
            </p>
    </div>

    <div class="uk-margin">
        <label for="form-user-verification" class="uk-form-label">{{ 'Verification' | trans }}</label>
        <div class="uk-form-controls uk-form-controls-text">
            <label><input class="uk-checkbox" id="form-user-verification" type="checkbox" v-model="config.require_verification"> {{ 'Require e-mail verification when a guest creates an account.' | trans }}</label>
        </div>
    </div>

    <div class="uk-margin">
        <label for="form-redirect" class="uk-form-label">{{ 'Login Redirect' | trans }}</label>
        <div class="uk-form-controls">
           <input-link class="uk-link-text uk-inline" id="form-redirect" input-class="uk-form-width-large" v-model="config.login_redirect"></input-link>
        </div>
    </div>

</div>
