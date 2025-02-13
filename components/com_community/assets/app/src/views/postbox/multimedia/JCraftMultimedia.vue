<template>
    <div class="joms-postbox-file-wrapper">
        <j-uploader 
            ref="uploader"
            :config="config" 
            @filesChange="onFilesChange">
        </j-uploader>
      <JFileComposer ref="composer" />
      <JVideoFetcher
          ref="fetcher"
          v-if="!type || type === 'fetch'"
          :videoType="type"
          @typeChange="setType"
          @reset="reset">
      </JVideoFetcher>
        <JFileMiniBar
            @showMoodPicker="showMoodPicker" 
            @openUploader="openUploader"
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
            <JPrivacyPicker
                v-if="privacy" 
                v-click-outside="hidePrivacy" 
                @hidePrivacy="hidePrivacy"
                @setPrivacy="setPrivacy"/>
        </keep-alive>
    </div>
</template>

<script>
import JVideoFetcher from '../video/JVideoFetcher.vue';
import JUploader from '../_components/JUploader.vue';
import JFileComposer from './JFileComposer.vue';
import JFileMiniBar from './JCraftMultimediaMiniBar.vue';
import JMoodPicker from '../_components/JMoodPicker.vue';
import JPrivacyPicker from '../_components/JPrivacyPicker.vue';
import language from '../../../utils/language';
import { constants } from '../../../utils/constants';

export default {
    components: {
        JUploader,
        JFileComposer,
        JFileMiniBar,
        JMoodPicker,
        JPrivacyPicker,
      JVideoFetcher,
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
            defaultType,
            type: defaultType,
            composer: false,
            moodPicker: false,
            privacy: false,
        };
    },

    methods: {
        validate() {
            const limit = constants.get('conf.limitfile');
            const uploaded = constants.get('conf.uploadedfile');
            const files = this.$store.state.file.attachment.id;

          if (!files && !this.$refs.fetcher.validate()) {
            return alert('Выберите файл или добавьте ссылку на видео');
          }

            if (files && files.length > limit - uploaded) {
                return alert(language('photo.upload_limit_exceeded'));
            }

          if (files && files.length >= 1) {
            this.post();
          }
            this.$refs.fetcher.validate();
        },

      setType(value) {
        this.type = value;
      },

        post() {
            const DATA = Joomla.getOptions('com_community');
            const filterParams = DATA.stream_filter_params ? JSON.stringify(DATA.stream_filter_params) : '';

            const state = this.$store.state.file;
            const content = state.content;
            const attachments = JSON.stringify(state.attachment);
            const rawData = [content, attachments, filterParams];

            this.$store.dispatch('post', rawData).then(() => {
                this.reset();
            });
        },

        showMoodPicker() {
            this.moodPicker = true;
        },

        hideMoodPicker() {
            this.moodPicker = false;
        },

        openUploader() {
            this.$refs.uploader.open();
        },

        showPrivacy() {
            this.privacy = true;
        },

        hidePrivacy() {
            this.privacy = false;
        },

        setPrivacy(privacy) {
            this.$store.commit('file/setPrivacy', privacy);
        },

        onFilesChange(files) {
            this.composer = !!files.length;

            this.$store.commit('setFree', !files.length);
            this.$store.commit('file/setFile', files);

            if (!files.length) {
                this.$store.commit('file/reset');
            }
        },

        setMood(mood) {
            this.$store.commit('file/setMood', mood);
        },

        reset() {
           // this.$refs.composer.reset(); move to vidro fetcher
            this.$refs.uploader.reset();
            this.$refs.fetcher && this.$refs.fetcher.reset();

            this.$store.commit('file/reset');
            this.$store.commit('setFree', true);
        },
    }
}
</script>
