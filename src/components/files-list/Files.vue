<template>
  <div class="panel panel-flat"
       v-if="allFiles.length"
  >
    <div class="panel-heading">
      <h5 class="panel-title">In-Club Marketing Downloads</h5>
    </div>

    <div class="panel-body">
      <section class="mock-ups">
        <ul class="mock-ups__list">

          <li class="mock-ups__list-item"
              v-for="file in allFiles"
              :key="file.id"
          >

            <div class="mock-up__wrap"
                 v-if="+file.enabled"
            >

              <div class="mock-up__body">
                <div class="mock-up__body-inner">
                  <picture v-if="file.previewImage">
                    <img
                      :src="file.previewImage"
                      :alt="file.title"
                    >
                  </picture>
                </div>
              </div>

              <div class="mock-up__footer">
                <h6>{{ file.title }}</h6>

                <span></span>

                <a :href="'/actions/print-plugin/static/download-static?id=' + file.id">
                  <div class="cms-btn">Download</div>
                </a>

                <a :id="'download-link-' + file.id"
                   href=""
                   :download="file.fileType === 'pdf' ? file.title : file.title + '.html'"
                   style="display: none !important;"
                ></a>

              </div>

            </div>

            <div class="mock-up__wrap --disabled"
                 v-else
            >

              <div class="mock-up__body">
                <div class="mock-up__body-inner">
                  <picture>
                    <img
                      :src="file.previewImage || '/img/preview.svg'"
                      :alt="file.title"
                    >
                  </picture>
                </div>

                <div class="mock-up__body-hover"></div>
              </div>

              <div class="mock-up__footer">
                <h6>{{ file.title }}</h6>

                <span></span>

                <div class="cms-btn --dis">Available Soon</div>

              </div>
            </div>

          </li>

        </ul>
      </section>

    </div>
  </div>
</template>

<script>
  import PrintPluginService from '../../../../shared/js/PrintPluginService'

  export default {
    name: "Files",

    data: () => ({
      allFiles: [],
      loadingFiles: true,
      printPluginService: new PrintPluginService(),
      downloadingFile: null,
    }),

    async mounted() {
      this.allFiles = await this.printPluginService.getAllFiles() || []
      this.loadingFiles = false
    },
  }
</script>