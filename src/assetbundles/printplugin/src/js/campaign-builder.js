import CreatePDFService from './CreatePDFService/CreatePDFService'

document.addEventListener("DOMContentLoaded", () => {
  if (document.getElementById('campaign-builder')) {
    const campaignBuilder = document.getElementById('campaign-builder')

    const PDFService = new CreatePDFService

    const getTemplateAndSettings = async (promotionId, layoutId) => {
      const { backHtml, html, settings } = await fetch(
        '/actions/print-plugin/campaign-builder/get-html-campaign?' +
        `id=${ promotionId }&layout=${ layoutId }`
      ).then(response => response.json())

      return { backHtml, html, settings } || {}
    }

    campaignBuilder.addEventListener('click', ({ target }) => {
      if (target.classList.contains('create-pdf') || target.classList.contains('create-jpeg')) {
        target.classList.add('--disabled')

        try {
          const promotionId = target.dataset.id
          const layoutId = target.closest('tr').querySelector('[name="layout"]').value
          getTemplateAndSettings(promotionId, layoutId)
            .then(({ backHtml, html, settings = {} }) => {
              PDFService.setOptions(settings)
              console.log(backHtml.trim().length)
              if (target.classList.contains('create-pdf')) {
                PDFService.createPDF(html).then(() => {
                  if (backHtml.trim().length) {
                    PDFService.createPDF(backHtml)
                  }
                })
              } else if (target.classList.contains('create-jpeg')) {
                PDFService.createJPEG(html).then(jpeg => {
                  const downloadLink = document.getElementById(`download-link-${ target.dataset.id }`)
                  downloadLink.href = jpeg
                  downloadLink.click()

                  if (backHtml.trim().length) {
                    PDFService.createJPEG(backHtml).then(jpeg => {
                      downloadLink.href = jpeg
                      downloadLink.click()
                    })
                  }
                })
              }

              target.classList.remove('--disabled')
            })
        } catch (e) {
          console.log('Error!!! ', e)
          target.classList.remove('--disabled')
        }
      }
    })
  }
})