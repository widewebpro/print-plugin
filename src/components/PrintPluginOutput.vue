<template>
  <div class="content">

    <div class="pdf-container-by-print"></div>

    <PdfsList
      :displayedCardsLength="displayedCardsLength"
      @setLoadingPdfsList="setLoadingPdfsList($event)"
    />

    <FilesList
      @setLoadingFilesList="setLoadingFilesList($event)"
    />

    <transition name="fade">
      <div class="alert-popup flex items-center justify-center fixed top-0 right-0 bottom-0 left-0"
           v-if="loading">
        <div class="alert-popup__inner relative">
          <div class="flex flex-col items-center">
            <p class="alert-popup__title">Please wait</p>
            <div class="loading-animate">Loading...</div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
  import PdfsList from './pdfs-list/PdfsList.vue'
  import FilesList from './files-list/FilesList.vue'

  export default {
    name: 'PrintPluginOutput',

    props: {
      displayedCardsLength: {
        type: Number,
        default: 8
      }
    },

    data: () => ({
      loadingPdfsList: true,
      loadingFilesList: true,
    }),

    computed: {
      loading: function () {
        return this.loadingPdfsList || this.loadingFilesList
      }
    },

    methods: {
      setLoadingPdfsList(bul) {
        this.loadingPdfsList = bul
      },
      setLoadingFilesList(bul) {
        this.loadingFilesList = bul
      },
    },

    components: { PdfsList, FilesList }
  }
</script>
