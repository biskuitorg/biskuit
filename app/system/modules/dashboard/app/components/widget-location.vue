<template>
    <div>
        <div class="uk-position-top-right">
            <ul class="uk-subnav pk-subnav-icon">
                <li v-show="!editing">
                    <a class="pk-icon-contrast pk-icon-edit pk-icon-hover uk-hidden" :title="$trans('Edit')" data-uk-tooltip="{delay: 500}" @click.prevent="$parent.edit"></a>
                </li>
                <li v-show="!editing">
                    <a class="pk-icon-contrast pk-icon-handle pk-icon-hover uk-hidden uk-sortable-handle" :title="$trans('Drag')" data-uk-tooltip="{delay: 500}"></a>
                </li>
                <li v-show="editing">
                    <a class="pk-icon-delete pk-icon-hover" :title="$trans('Delete')" data-uk-tooltip="{delay: 500}" @click.prevent="$parent.remove" v-confirm="'Delete widget?'"></a>
                </li>
                <li v-show="editing">
                    <a class="pk-icon-check pk-icon-hover" :title="$trans('Close')" data-uk-tooltip="{delay: 500}" @click.prevent="$parent.save"></a>
                </li>
            </ul>
        </div>

        <form  v-show="editing" @submit.prevent>

        <div class="uk-margin">


              <label class="typo__label" for="location">{{ 'Location' | trans }}</label>
              <multiselect  v-model="selectedCity" id="location" ref="location" label="name" track-by="id" placeholder="Type to search" open-direction="bottom" :showNoOptions="false" :custom-label="customLabel" :options="cities" :multiple="false" :searchable="true" :loading="isLoading" :internal-search="false" :clear-on-select="false" :close-on-select="true" :options-limit="300" :limit="3" :limit-text="limitText" :max-height="600" :show-no-results="false" :hide-selected="false" @search-change="asyncFind" @select="updateWidget">
                <template slot="tag" slot-scope="{ option, remove }"><span class="custom__tag"><span>{{ option.name }}</span><span class="custom__remove" @click="remove(option)">x</span></span></template>
                <template slot="clear" slot-scope="props">
                  <div class="multiselect__clear" v-if="selectedCity.length" @mousedown.prevent.stop="clearAll(props.search)"></div>
                </template><span slot="noResult">{{'No location found.' | trans }}</span>
              </multiselect>
        </div>

            <div class="uk-margin">
                <span class="uk-form-label">{{ 'Unit' | trans }}</span>

                <div class="uk-form-controls uk-form-controls-text">
                    <p class="uk-form-controls uk-margin-small">
                        <label><input type="radio" value="metric" v-model="widget.units"> {{ 'Metric' | trans }}</label>
                    </p>

                    <p class="uk-form-controls uk-margin-large-bottom">
                        <label><input type="radio" value="imperial" v-model="widget.units"> {{ 'Imperial' | trans }}</label>
                    </p>
                </div>
            </div>

        </form>

        <div class="bk-panel-background uk-light" v-if="status != 'loading'">
            <h1 class="uk-margin-large-top uk-margin-small-bottom uk-text-center pk-text-xlarge" v-if="time">{{ time | date(format) }}</h1>

            <h2 class="uk-text-center uk-h4 uk-margin-remove" v-if="time">{{ time | date('longDate') }}</h2>
            <div class="uk-margin-large-top uk-flex uk-flex-middle uk-flex-between uk-flex-wrap">
                <h3 class="uk-margin-remove" v-if="widget.city">{{ widget.city }}</h3>
                <h3 class="uk-flex uk-flex-middle uk-margin-remove" v-if="status=='done'"> {{ temperature }} <img class="uk-margin-small-left" :src="icon" width="25" height="25" alt="Weather"></h3>
            </div>
        </div>
        <div class="uk-text-center" v-else>
            <v-loader></v-loader>
        </div>
    </div>
</template>

<script>
import Multiselect from 'vue-multiselect';
    export default {
        components: {
          Multiselect
        },
        type: {
            id: 'location',
            label: 'Location',
            disableToolbar: false,
            description: () => {},
            defaults: {
                units: 'metric'
            }
        },

        props: ['widget', 'editing'],

        data() {
            return {
                status: '',
                timezone: {},
                icon: '',
                temp: 0,
                time: 0,
                format: 'shortTime',
                selectedCity: [],
                cities: [],
                isLoading: false
            };
        },

        mounted() {
          const vm = this;
            this.load();
            this.timer = setInterval(this.updateClock(), 60 * 1000);
        },

        watch: {
            'widget.uid': {
                handler(uid) {
                    if (uid === undefined) {
                        this.$set(this.widget, 'uid', '');
                        this.$parent.save();
                        this.$parent.edit(true);
                    }

                    if (!uid) return;
                    this.load();
                },
                immediate: true

            },

            'timezone': 'updateClock'
        },

        computed: {
            location() {
                return this.widget.city ? this.widget.city + ', ' + this.widget.country : '';
            },

            temperature() {
                if (this.widget.units !== 'imperial') {
                    return Math.round(this.temp) + ' °C';
                }
                return Math.round(this.temp * (9 / 5) + 32) + ' °F';
            }
        },

        methods: {
            load() {
                if (!this.widget.uid) {
                    return;
                }

                this.$http.get('admin/dashboard/weather', { params: { action: 'weather', data: { id: this.widget.uid, units: 'metric' } }, cache: 60 }).then(
                    function (res) {
                        const { data } = res;
                        if (data.cod == 200) {
                            this.init(data)
                        } else {
                            this.status = 'error';
                        }

                    },
                    function () {
                        this.status = 'error';
                    }
                );
            },

            init(data) {
                this.temp = data.main.temp;
                this.icon = this.getIconUrl(data.weather[0].icon);
                this.status = 'done';
            },

            getIconUrl(icon) {
                const icons = {
                    '01d': 'sun.svg',
                    '01n': 'moon.svg',
                    '02d': 'cloud-sun.svg',
                    '02n': 'cloud-moon.svg',
                    '03d': 'cloud.svg',
                    '03n': 'cloud.svg',
                    '04d': 'cloud.svg',
                    '04n': 'cloud.svg',
                    '09d': 'drizzle-sun.svg',
                    '09n': 'drizzle-moon.svg',
                    '10d': 'rain-sun.svg',
                    '10n': 'rain-moon.svg',
                    '11d': 'lightning.svg',
                    '11n': 'lightning.svg',
                    '13d': 'snow.svg',
                    '13n': 'snow.svg',
                    '50d': 'fog.svg',
                    '50n': 'fog.svg'
                };
                return this.$url('app/system/modules/dashboard/assets/images/weather-{icon}', { icon: icons[icon] });
            },

            updateClock() {
                const offset = this.timezone.offset || 0;
                const date = new Date();
                const time = offset ? new Date(date.getTime() + date.getTimezoneOffset() * 60000 + offset * 1000) : new Date();
                this.time = time;
                return this.updateClock;
            },


          destroyed() {
              clearInterval(this.timer);
          },

          limitText (count) {
            return 'and {count} other cities';
          },

          asyncFind (query) {
            if(query.length >=3) {
              this.isLoading = true;
              this.$http.get('admin/dashboard/weather', { params: { action: 'find', data: { q: query, type: 'like' } } }).then(
                  function (res) {
                      const { data } = res;
                      if (data.cod == 200) {
                        this.list = data.list || [];
                        this.cities = this.list;
                      } else {
                          this.status = 'error';
                      }
                      this.isLoading = false;
                  });
            }
          },

          customLabel ( { name, sys }) {
            return `${name} – ${sys['country']}`
          },

          updateWidget(location)  {
            this.$set(this.widget, 'uid', location.id);
            this.$set(this.widget, 'city', location.name);
            this.$set(this.widget, 'country', location.sys.country);
            this.$set(this.widget, 'coords', location.coord);
          },

          clearAll () {
            this.selectedCity = [];
          },
    }
  }
</script>
