import { fetchTemplateAndSettingsService } from './fetchTemplateAndSettingsService'
import CreatePDFService from './CreatePDFService'

document.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('pdfs-list')) {
    const pdfsList = document.getElementById('pdfs-list')

    const PDFService = new CreatePDFService

    const getTemplateAndSettings = async (id) => {
      const getTemplateAndSettings = await fetchTemplateAndSettingsService(id)
      return getTemplateAndSettings || {}
    }

    pdfsList.addEventListener('click', event => {
      if (event.target.classList.contains('create-pdf')) {
        try {
          event.target.classList.add('--disabled')

          getTemplateAndSettings(event.target.dataset.id)
          .then(({ html = `<h1>Undefined template</h1>`, settings = {} }) => {
            PDFService.setOptions(settings)

            PDFService.createPDF(html).then(() => {
              event.target.classList.remove('--disabled')
            })
          })
        } catch (e) {
          console.log('Error!!! ', e)
          event.target.classList.remove('--disabled')
        }
      } else if (event.target.classList.contains('create-jpeg')) {
        try {
          event.target.classList.add('--disabled')

          getTemplateAndSettings(event.target.dataset.id)
          .then(({ html = `<h1>Undefined template</h1>`, settings = {} }) => {
            PDFService.setOptions(settings)

            PDFService.createJPEG(html).then(jpeg => {
              const downloadLink = document.getElementById(`download-link-${ event.target.dataset.id }`)
              downloadLink.href = jpeg
              downloadLink.click()
              event.target.classList.remove('--disabled')
            })
          })
        } catch (e) {
          console.log('Error!!! ', e)
          event.target.classList.remove('--disabled')
        }
      }

    })
  }
})

