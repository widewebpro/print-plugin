<template>
  <div class="panel panel-flat">
    <div class="panel-heading">
      <h5 class="panel-title">Marketing Downloads</h5>
    </div>

    <div class="panel-body">
      <span v-if="loadingMockUps">Loading...</span>

      <span v-else-if="!loadingMockUps && !allMockUps.length">You haven't any mock-ups</span>

      <section class="mock-ups"
               v-else-if="allMockUps.length && !loadingMockUps"
      >
        <ul class="mock-ups__list">

          <li class="mock-ups__list-item"
              v-for="mockUp in displayedMockUps"
              :key="mockUp.id"
          >

            <div class="mock-up__wrap"
                 v-if="+mockUp.enabled"
            >

              <div class="mock-up__body">
                <div class="mock-up__body-inner">
                  <div class="mock-up__preview-html"
                       :id="mockUp.id"
                       v-if="mockUp.previewHtml"
                  ></div>
                  <picture v-else-if="mockUp.previewImage">
                    <img
                      :src="mockUp.previewImage"
                      :alt="mockUp.title"
                    >
                  </picture>
                </div>

                <div class="mock-up__body-hover"
                     @click.prevent="getPreview(mockUp.id)"
                >
                  <div class="mock-up__body-hover-img">
                    <picture>
                      <img src="/img/preview-img.svg" alt="Get preview">
                    </picture>
                  </div>
                </div>
              </div>

              <div class="mock-up__footer">
                <h6>{{ mockUp.title }}</h6>

                <span
                  v-if="mockUp.type === 'a'"
                >{{ mockUp.settings | filterMockUpTypeA }}</span>

                <span
                  v-else
                >{{ mockUp.settings | filterMockUpSettings }} {{ mockUp.type }}</span>

                <div class="cms-btn"
                     v-if="mockUp.fileType === 'pdf'"
                     @click="onDownloadJPEG(mockUp.id)"
                >
                  <transition name="fade" mode="out-in">
                    <div class="mockup-downloading flex items-center justify-center"
                         v-if="+downloadingJPEG === +mockUp.id"
                    >
                      <span>↓</span>
                      <span style="animation-delay: 0.1s;">↓</span>
                      <span style="animation-delay: 0.3s;">↓</span>
                      <span style="animation-delay: 0.4s;">↓</span>
                    </div>
                    <span v-else>Download JPEG</span>
                  </transition>
                </div>

                <a :id="'download-link-' + mockUp.id"
                   href=""
                   :download="mockUp.fileType === 'pdf' ? mockUp.title : mockUp.title + '.html'"
                   style="display: none !important;"
                ></a>


                <div class="cms-btn"
                     v-if="mockUp.fileType === 'pdf'"
                     @click="onDownloadPDF(mockUp.id)"
                >
                  <transition name="fade" mode="out-in">
                    <div class="mockup-downloading flex items-center justify-center"
                         v-if="+downloadingPDF === +mockUp.id"
                    >
                      <span>↓</span>
                      <span style="animation-delay: 0.1s;">↓</span>
                      <span style="animation-delay: 0.3s;">↓</span>
                      <span style="animation-delay: 0.4s;">↓</span>
                    </div>
                    <span v-else>Download PDF</span>
                  </transition>
                </div>


                <div class="cms-btn"
                     v-if="mockUp.fileType === 'html'"
                     @click="onDownloadHTML(mockUp.id)"
                >
                  <transition name="fade" mode="out-in">
                    <div class="mockup-downloading flex items-center justify-center"
                         v-if="+downloadingHTML === +mockUp.id"
                    >
                      <span>↓</span>
                      <span style="animation-delay: 0.1s;">↓</span>
                      <span style="animation-delay: 0.3s;">↓</span>
                      <span style="animation-delay: 0.4s;">↓</span>
                    </div>
                    <span v-else>Download</span>
                  </transition>
                </div>
              </div>

            </div>

            <div class="mock-up__wrap --disabled"
                 v-else
            >

              <div class="mock-up__body">
                <div class="mock-up__body-inner">
                  <picture>
                    <img
                      :src="mockUp.previewImage || '/img/preview.svg'"
                      :alt="mockUp.title"
                    >
                  </picture>
                </div>

                <div class="mock-up__body-hover"></div>
              </div>

              <div class="mock-up__footer">
                <h6>{{ mockUp.title }}</h6>

                <span
                  v-if="mockUp.type === 'a'"
                >{{ mockUp.settings | filterMockUpTypeA }}</span>

                <span
                  v-else
                >{{ mockUp.settings | filterMockUpSettings }} {{ mockUp.type }}</span>

                <div class="cms-btn --dis">Available Soon</div>
              </div>

            </div>

          </li>

        </ul>

        <div class="flex justify-center mt-6"
             v-if="displayedMockUpsLength < allMockUps.length"
             @click="displayedMockUpsLength += 8"
        >
          <div class="cms-btn">Show More</div>
        </div>
      </section>

    </div>

    <div class="mock-up__preview" v-if="previewIsOpen">
      <div class="mock-up__preview-inner" v-if="previewImg">
        <picture>
          <img :src="previewImg">
        </picture>
      </div>
      <div class="mock-up__preview-loading" v-else>Loading...</div>
      <div class="mock-up__preview-close"
           v-if="previewImg"
           @click="onClosePreview"
      >
        <picture><img src="/img/close.png" alt="Close"></picture>
      </div>
    </div>

  </div>
</template>

<script>
  import PrintPluginService from '../../functions/PrintPluginService'
  import { fetchTemplateAndSettingsService } from '../../functions/PrintPluginService/fetchTemplateAndSettingsService'

  export default {
    name: "PdfsList",

    props: {
      displayedCardsLength: {
        type: Number,
        default: 8
      }
    },

    data: () => ({
      allMockUps: [],
      displayedMockUpsLength: 8,
      loadingMockUps: true,
      loading: true,
      printPluginService: new PrintPluginService(),
      downloadingJPEG: null,
      downloadingPDF: null,
      downloadingHTML: null,
      previewIsOpen: false,
      previewImg: null,
    }),

    computed: {
      displayedMockUps() {
        return this.allMockUps.slice(0, this.displayedMockUpsLength)
      }
    },

    async mounted() {
      this.displayedMockUpsLength = this.displayedCardsLength
      this.allMockUps = await this.printPluginService.getAllMockUps() || []
      this.loadingMockUps = false
      this.loading = false

      this.displayedMockUps
        .filter((mockUp) => +mockUp.enabled)
        .forEach((mockUp) => {
          mockUp.previewHtml = this.getHTML(mockUp.id)
        })

      document.addEventListener('click', ({target}) => {
        if (this.previewImg &&
          !target.closest('.mock-up__preview') &&
          !target.classList.contains('.mock-up__preview')
        ) {
          this.onClosePreview()
        }
      })

      document.addEventListener('keyup', ({keyCode}) => {
        if (this.previewImg && keyCode === 27) {
          this.onClosePreview()
        }
      })
    },

    watch: {
      loading() {
        this.$emit('setLoadingPdfsList', this.loading)
      },

      displayedMockUps() {
        this.displayedMockUps
          .filter((mockUp) => +mockUp.enabled && !mockUp.previewHtml)
          .forEach((mockUp) => {
            mockUp.previewHtml = this.getHTML(mockUp.id)
          })
      }
    },

    methods: {
      async getHTML(mockUpId) {
        const { html } = await fetchTemplateAndSettingsService(mockUpId)
        this.allMockUps = this.allMockUps.map(mockUp => {
          if (mockUp.id === mockUpId) {
            mockUp = {...mockUp, previewHtml: html}
          }
          return mockUp
        })

        if (html) {
          const container = document.getElementById(mockUpId)
          const containerWidth = container.offsetWidth
          const containerHeight = 167

          const defaultStyle = `
            <style>
              html {
                overflow: hidden !important;
              }
              body {
                overflow: hidden !important;
                margin: 0;
                padding: 0;
              }
            </style>
          `

          const temporaryContainer = document.querySelector('.pdf-container-by-print')
          temporaryContainer.innerHTML = html + defaultStyle

          const inner = temporaryContainer.firstElementChild
          const innerWidth = inner.offsetWidth
          const innerHeight = inner.offsetHeight

          const scaleWidth = containerWidth / innerWidth
          const scaleHeight = containerHeight / innerHeight
          const scale = scaleWidth < scaleHeight ? scaleWidth : scaleHeight

          if (scaleWidth > scaleHeight) {
            const innerContentWidth = innerWidth * scale
            const translateX = (containerWidth - innerContentWidth) / 2 / scale

            inner.style.transform =
              `scale(${scale}) translateX(${translateX}px)`
          } else {
            const innerContentHeight = innerHeight * scale
            const translateY = (containerHeight - innerContentHeight) / 2 / scale

            inner.style.transform =
              `scale(${scale}) translateY(${translateY}px)`
          }

          inner.style.transformOrigin = 'left top'

          const iframe = document.createElement('iframe')
          container.appendChild(iframe)
          iframe.contentWindow.document.open()
          iframe.contentWindow.document.write(temporaryContainer.innerHTML)
          iframe.contentWindow.document.close()
          temporaryContainer.innerHTML = ''
        }
      },

      async onDownloadJPEG(mockUpId) {
        if (this.downloadingJPEG) return
        this.downloadingJPEG = mockUpId
        await this.printPluginService.downloadJPEG(mockUpId)
        setTimeout(() => {
          this.downloadingJPEG = null
        }, 5000)
      },

      async onDownloadPDF(mockUpId) {
        if (this.downloadingPDF) return
        this.downloadingPDF = mockUpId
        await this.printPluginService.downloadPDF(mockUpId)
        setTimeout(() => {
          this.downloadingPDF = null
        }, 5000)
      },

      async onDownloadHTML(mockUpId) {
        if (this.downloadingHTML) return
        this.downloadingHTML = mockUpId
        this.printPluginService.downloadHTML(mockUpId)
        setTimeout(() => {
          this.downloadingHTML = null
        }, 5000)
      },

      async getPreview(mockUpId) {
        this.loading = true

        this.previewIsOpen = true
        this.previewImg = await this.printPluginService.getPreview(mockUpId)

        this.loading = false
      },

      onClosePreview() {
        this.previewIsOpen = false
        this.previewImg = null
      }
    },

    filters: {
      filterMockUpSettings(value) {
        const settingsObj = JSON.parse(value)
        if ((settingsObj.height || '').length && (settingsObj.width || '').length) {
          return `${settingsObj.width}x${settingsObj.height}`
        }
        return ''
      },
      filterMockUpTypeA(value) {
        const settingsObj = JSON.parse(value)
        if (settingsObj.format) {
          return `Format ${settingsObj.format.toUpperCase()}`
        }
        return ''
      }
    },
  }
</script>
