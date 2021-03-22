<template>
    <div>
        <form class="pk-panel-teaser uk-form uk-form-stacked" v-if="editing">

            <div class="uk-margin">
                <label for="form-feed-title" class="uk-form-label">{{ 'Title' | trans }}</label>

                <div class="uk-form-controls">
                    <input id="form-feed-title" class="uk-width-1-1" type="text" name="widget[title]" v-model="widget.title">
                </div>
            </div>

            <div class="uk-margin">
                <label for="form-feed-url" class="uk-form-label">{{ 'URL' | trans }}</label>

                <div class="uk-form-controls">
                    <input id="form-feed-url" class="uk-width-1-1" type="text" name="url" v-model.lazy="widget.url">
                </div>
            </div>

            <div class="uk-margin">
                <label for="form-feed-count" class="uk-form-label">{{ 'Number of Posts' | trans }}</label>

                <div class="uk-form-controls">
                    <select id="form-feed-count" class="uk-width-1-1" v-model.number="widget.count">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                    </select>
                </div>
            </div>

            <div class="uk-margin">
                <span class="uk-form-label">{{ 'Post Content' | trans }}</span>

                <div class="uk-form-controls uk-form-controls-text">
                    <p class="uk-form-controls-condensed">
                        <label><input type="radio" value="" v-model="widget.content"> {{ "Don't show" | trans }}</label>
                    </p>

                    <p class="uk-form-controls-condensed">
                        <label><input type="radio" value="1" v-model="widget.content"> {{ 'Show on all posts' | trans }}</label>
                    </p>

                    <p class="uk-form-controls-condensed">
                        <label><input type="radio" value="2" v-model="widget.content"> {{ 'Only show on first post.' | trans }}</label>
                    </p>
                </div>
            </div>

        </form>

        <div v-if="status != 'loading'">

            <h3 class="uk-text-large uk-text-lighter" v-if="widget.title">{{ widget.title }}</h3>

            <ul class="uk-list uk-list-divider uk-margin-remove">
                <li v-for="(item, index) in count(feed.items)">
                    <a :href="item.link" target="_blank">{{ item.title }}</a> <span :title="$date(item.isoDate, 'medium')" class="uk-text-muted uk-text-nowrap">{{ item.isoDate | relativeDate }}</span>

                    <p class="uk-margin-small-top" v-if="widget.content == '1'">{{ item.contentSnippet }}</p>

                    <p class="uk-margin-small-top" v-if="widget.content == '2'">{{ index == 0 ? item.contentSnippet : '' }}</p>
                </li>
            </ul>

            <div v-if="status == 'error'">{{ 'Unable to retrieve feed data.' | trans }}</div>

            <div v-if="!widget.url && !editing">{{ 'No URL given.' | trans }}</div>

        </div>

        <div class="uk-text-center" v-else>
            <v-loader></v-loader>
        </div>
    </div>
</template>

<script>
    import RSSParser from 'rss-parser';

    export default {
        type: {
            id: 'feed',
            label: 'Feed',
            description: () => {},
            defaults: {
                count: 5,
                url: 'https://biskuit.org/blog/feed',
                content: ''
            }
        },

        props: ['widget', 'editing'],

        data() {
            return {
                status: '',
                feed: {}
            }
        },

        watch: {
            'widget.url': function (url) {
                if (!url) {
                    this.$parent.edit(true);
                }
                this.load();
            },
            'widget.count': function (count, old) {
                const items = this.feed.items;
                if (items && count > old && count > items.length) {
                    this.load();
                }
            }
        },

        mounted() {
            if (this.widget.url) {
                this.load();
            }
        },

        methods: {
            count(entries) {
                return entries ? entries.slice(0, this.widget.count) : [];
            },

            load() {
                const vm = this;
                this.feed = {};
                this.status = '';
                if (!this.widget.url) {
                    return;
                }

                this.status = 'loading';
                this.$http.get('admin/dashboard/feed', { params: { data: { url: this.widget.url} } }).then(
                    function (res) {
                        const { data } = res;
                        if (res.status == 200) {
                            const parser = new RSSParser();
                            parser.parseString(res.body)
                                .then((feed) => {
                                    vm.feed = feed;
                                    vm.status = 'done';
                                }, (err) => {
                                    vm.status = 'error';
                                }
                            );
                        } else {
                            this.status = 'error';
                        }
                    },
                    function () {
                        this.status = 'error';
                    }
                );
            }
        }
    }
</script>
