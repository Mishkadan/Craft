<template>
    <div class="joms-postbox-status">
        <JUrlPreview :data="preview" @removePreview="removePreview"/>
        <JStatusComposer ref="composer" @urlAppear="urlAppear"/>
      <div class="joms-postbox-file-wrapper">
        <j-uploader
            ref="uploader"
            :config="config"
            @filesChange="onFilesChange">
        </j-uploader>
      </div>
      <div class="vue--postto" v-if="havePages && !isEvent">
        <span>Опубликовать</span>
        <select v-model="selected">
          <option selected :value="toprofile">в ленте</option>
          <option v-for="option in postTo" :value="JSON.stringify(option)">
            в блоге {{ option.name }}
          </option>
        </select>
      </div>
        <JStatusMiniBar
            @openUploader="openUploader"
            @showMoodPicker="showMoodPicker" 
            @showLocation="showLocation"
            @showPrivacy="showPrivacy"
            @reset="reset"
            @validate="validate" />
        <keep-alive>
            <JMoodPicker 
                v-if="moodPicker" 
                v-click-outside="hideMoodPicker"
                @hideMoodPicker="hideMoodPicker"
                @setMood="setMood" />
        </keep-alive>
        <keep-alive>
            <JLocationPicker
                v-if="location" 
                v-click-outside="hideLocation"
                :locationName="locationName"
                @setLocation="setLocation"
                @removeLocation="removeLocation" />
        </keep-alive>
      <keep-alive>
            <JPrivacyPicker
                v-if="privacy" 
                v-click-outside="hidePrivacy" 
                @hidePrivacy="hidePrivacy"
                @setPrivacy="setPrivacy"/>
      </keep-alive>
    </div>
</template>

<script>
import JStatusComposer from './JStatusComposer.vue';
import JStatusMiniBar from './JStatusMiniBar.vue';
import JUploader from '../_components/JUploader.vue';
import JFileComposer from '../multimedia/JFileComposer.vue';
import JMoodPicker from '../_components/JMoodPicker.vue';
import JLocationPicker from '../_components/JLocationPicker.vue';
import JPrivacyPicker from '../_components/JPrivacyPicker.vue';
import JUrlPreview from '../_components/JUrlPreview.vue';
import unescape from 'unescape';
import {constants} from "../../../utils/constants";
import language from "../../../utils/language";

export default {
    components: {
        JStatusComposer,
        JStatusMiniBar,
        JUploader,
        JFileComposer,
        JMoodPicker,
        JLocationPicker,
        JPrivacyPicker,
        JUrlPreview,
    },

    data() {

      const baseUrl = Joomla.getOptions('com_community').base_url;
      const { enablevideosupload } = constants.get('conf');
      const defaultType = enablevideosupload ? '' : 'fetch';
      const {
        isProfile,
        isGroup,
        isEvent,
        isPage
      } = constants.get('settings');
      const conf = constants.get('conf');

      const toprofile = JSON.stringify({'id': constants.get('uid'), 'element': 'profile'});

      const postTo = constants.get('postbox.postTo');

      const havePages = postTo.length >=1;
      /***
      let target = this.$store.state.status.attachment.target;

      let selected = 'profile';

      for(let i = 0; i<postTo.length; i++) {
        if(postTo[i].id == target) {
          selected = postTo[i];
        }
      }
      ***/
      let fileTypes = [];
      let maxFilesize = 1;
      if (isProfile) {
        fileTypes = conf.file_activity_ext.split(',');
        maxFilesize = conf.file_sharing_activity_max;
      }
      if (isGroup) {
        fileTypes = conf.file_group_ext.split(',');
        maxFilesize = conf.file_sharing_group_max;
      }
      if (isPage) {
        fileTypes = conf.file_page_ext.split(',');
        maxFilesize = conf.file_sharing_page_max;
      }
      if (isEvent) {
        fileTypes = conf.file_event_ext.split(',');
        maxFilesize = conf.file_sharing_event_max;
      }

        return {
          config: {
            maxFiles: constants.get('conf.num_file_per_upload'),
            maxFilesize: maxFilesize,
            dropAreaText: language('file.drop_to_upload'),
            uploadAreaText: language('file.upload_button'),
            previewApi: baseUrl + 'index.php?option=com_community&view=files&task=multiUpload&type=activities',
            fileTypes: fileTypes,
            createImageThumbnails: true,
            removeTempApi: 'system,ajaxDeleteTempFile',
            batch_notice: language('file.batch_notice'),
            max_upload_size_error: language('file.max_upload_size_error').replace('##maxsize##', maxFilesize),
            file_type_not_permitted: language('file.file_type_not_permitted'),
          },
            selected: toprofile,
            postTo,
            toprofile,
            havePages,
            isEvent,
            moodPicker: false,
            location: false,
            privacy: false,
            preview: {
                url: '',
                title: '',
                desc: '',
                image: '',
            },
        }
    },

    computed: {
        locationName() {
            const location = this.$store.state.status.attachment.location;
            return location.length === 3 ? location[0] : '';
        },
    },

    methods: {
        validate() {

          const limit = constants.get('conf.limitfile');
          const uploaded = constants.get('conf.uploadedfile');
          const files = this.$store.state.file.attachment.id;

          if (files && files.length > limit - uploaded) {
            return alert(language('photo.upload_limit_exceeded'));
          }
          this.post();
        },

        post(type) {
            const DATA = Joomla.getOptions('com_community');
            const filterParams = DATA.stream_filter_params ? JSON.stringify(DATA.stream_filter_params) : '';
            const selected = this.selected;
            const content = this.$store.state.status.content;
            const statusattachments = JSON.stringify(this.$store.state.status.attachment);
            const attachments = JSON.stringify(this.$store.state.file.attachment);

            const rawData = [ content, attachments, filterParams, statusattachments, selected ];

            this.$store.dispatch('post', rawData).then(() => {
                this.reset();
            });
        },

        onFilesChange(files) {
          this.composer = !!files.length;

          this.$store.commit('setFree', !files.length);
          this.$store.commit('file/setFile', files);

          if (!files.length) {
            this.$store.commit('file/reset');
          }
        },

        openUploader() {
          this.$refs.uploader.open();
        },

        showMoodPicker() {
            this.moodPicker = true;
        },

        hideMoodPicker() {
            this.moodPicker = false;
        },

        showLocation() {
            this.location = true;
        },

        hideLocation() {
            this.location = false;
        },

        showPrivacy() {
            this.privacy = true;
        },

        hidePrivacy() {
            this.privacy = false;
        },

        reset() {
            this.resetPreview();
            this.$refs.composer.reset();
            this.$refs.uploader.reset();
            this.$store.commit('status/reset');
            this.$store.commit('file/reset');
            this.$store.commit('setFree', true);
            this.selected = this.toprofile;
        },

        setPrivacy(privacy) {
            this.$store.commit('status/setPrivacy', privacy);
        },

        setMood(mood) {
            this.$store.commit('status/setMood', mood);
        },

        setLocation(location) {
            this.$store.commit('status/setLocation', location);
            this.hideLocation();
        },

        removeLocation() {
            this.$store.commit('status/setLocation', '');
            this.hideLocation();
        },

        urlAppear(url) {
            this.$store.commit('setLoading', true);

            joms.ajax({
                func: 'system,ajaxGetFetchUrl',
                data: [ url ],
                callback: json => {
                    const images = ( json.image || [] ).concat( json['og:image'] || [] );
                    
                    this.preview.image = images.length ? images[0] : '';
                    this.preview.url = url;
                    this.preview.title = unescape(json.title) || url;
                    this.preview.desc = unescape(json.description) || '';

                    this.setPreview(this.preview);
                    this.$store.commit('setLoading', false);
                }
            })
        },

        setPreview(data) {
            this.$store.commit('status/setPreview', data);
        },

        removePreview() {
            this.resetPreview();
            this.$store.commit('status/setPreview', false);
        },

        resetPreview() {
            this.preview.url = '';
            this.preview.title = '';
            this.preview.desc = '';
            this.preview.image = '';
        },
    },
}
</script>
