<?php $view->script('dashboard', 'system/dashboard:app/bundle/index.js', ['vue']) ?>

<?php $view->style('vue-multiselect', 'app/assets/vue-multiselect/vue-multiselect.min.css') ?>

<div id="dashboard" v-cloak>
    <div class="uk-margin uk-flex uk-flex-between uk-flex-wrap uk-flex-middle">
        <div>
            <div v-show="hasUpdate">
                <span class="pk-icon-bell uk-margin-small-right"></span>
                {{ 'Biskuit %version% is available.' | trans(update) }} <a href="admin/system/update">{{ 'Update now!' | trans }}</a>
            </div>
        </div>
        <div>
          <div class="uk-inline">
              <button class="uk-button uk-button-default" type="button">{{ 'Add Widget' | trans }}</button>
              <div uk-dropdown="mode: click">
                <ul class="uk-nav uk-dropdown-nav">
                    <li v-for="type in getTypes()">
                        <a class="uk-dropdown-close" @click="add(type)">{{ type.label }}</a>
                    </li>
                </ul>
              </div>
          </div>
        </div>
    </div>

    <div class="uk-grid uk-grid-medium uk-grid-match" uk-grid id="sortable-widgets" uk-sortable>
        <div class="uk-width-1-3@m " v-for="i in [0,1,2]">
            <ul class="uk-sortable bk-sortable" :data-column="i" uk-sortable="group: widgets">
                <li v-for="widget in getWidgetsForColumn(i)" :data-id="widget.id" :data-idx="widget.idx" :key="widget.id">
                    <panel class="uk-card and uk-card-default uk-card-body uk-visible-toggle" :widget="widget" :editing="editing[widget.id]" v-on:editing="editing[widget.id] = $event"></panel>
                </li>
            </ul>
        </div>
    </div>
</div>
