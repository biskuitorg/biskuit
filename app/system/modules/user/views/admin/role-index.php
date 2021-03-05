<?php $view->script('role-index', 'system/user:app/bundle/role-index.js', 'vue') ?>

<div id="roles" v-cloak>
    <div class="uk-grid" uk-grid>
        <div class="bk-width-sidebar">
            <div class="uk-panel" >

                <ul class="uk-sortable uk-nav uk-nav-default" id="sortable-roles" ref="sortableRoles" uk-sortable>
                    <li :id="role.id" class="uk-visible-toggle" v-for="role in orderedRoles" :class="{'uk-active': current.id === role.id}" :key="role.id">
                        <ul class="uk-subnav bk-subnav-icon uk-invisible-hover uk-float-right" v-if="!role.locked">
                            <li><a uk-icon="icon: pencil" :title="$trans('Edit')" data-uk-tooltip="{delay: 500}" @click="edit(role)"></a></li>
                            <li><a uk-icon="icon: trash" :title="$trans('Delete')" data-uk-tooltip="{delay: 500}" @click="remove(role)" v-confirm="'Delete role?'"></a></li>
                        </ul>
                        <a @click.prevent="config.role = role.id">{{ role.name }}</a>
                    </li>
                </ul>

                <p>
                    <a class="uk-button uk-button-default" @click.prevent="edit()">{{ 'Add Role' | trans }}</a>
                </p>
            </div>
        </div>
        <div class="bk-width-content">
            <h2>{{ current.name }}</h2>

            <div class="uk-overflow-auto uk-margin-large" v-for="(group, groubkey) in permissions" :key="groubkey">
                <table class="uk-table uk-table-hover uk-table-divider">
                    <thead>
                        <tr>
                            <th class="bk-table-min-width-200">{{ groubkey }}</th>
                            <th class="bk-table-width-minimum"></th>
                            <th class="bk-table-width-minimum"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(permission, permissionKey) in group" :class="{'uk-visible-toggle': permission.trusted}">
                            <td class="bk-table-text-break">
                                <span :title="$trans(permission.description)" data-uk-tooltip="{pos:'top-left'}">{{ permission.title | trans }}</span>
                            </td>
                            <td>
                                <i class="bk-icon-warning uk-invisible-hover" :title="$trans('Grant this permission to trusted roles only to avoid security implications.')" data-uk-tooltip v-if="permission.trusted"></i>
                            </td>
                            <td class="uk-text-center">
                                <span class="uk-position-relative" v-if="showFakeCheckbox(current, permissionKey)">
                                    <input class="input-checkbox" type="checkbox" checked disabled>
                                    <span class="uk-position-cover" v-if="!current.administrator" @click="addPermission(current, permissionKey)" @click="savePermissions(current)"></span>
                                </span>

                                <input class="input-checkbox" type="checkbox" :value="permissionKey" v-else v-model="current.permissions" @click="savePermissions(current)">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
      <v-modal ref="modal" uk-modal>
          <validation-observer v-slot="{ handleSubmit }" slim>
              <form class="uk-form uk-form-stacked" @submit.prevent="handleSubmit(save)">
                  <div class="uk-modal-title">
                      <h2>{{ (role.id ? 'Edit Role':'Add Role') | trans }}</h2>
                  </div>

                  <v-validated-input
                      id="form-name"
                      name="name"
                      rules="required"
                      label="Name"
                      :error-messages="{ required: 'Name cannot be blank.' }"
                      :options="{ elementClass: 'uk-width-1-1 uk-form-large' }"
                      v-model="role.name">
                  </v-validated-input>

                  <div class="uk-modal-footer uk-text-right">
                      <button class="uk-button uk-button-link uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
                      <button class="uk-button uk-button-link" type="submit">{{ 'Save' | trans }}</button>
                  </div>
              </form>
          </validation-observer>
      </v-modal>
</div>
