import Axios from "axios"
import { fetchTemplateAndSettingsService } from './fetchTemplateAndSettingsService'
import CreatePDFService from './CreatePDFService'

class PrintPluginService {
  constructor() {
    this.PDFService = new CreatePDFService
  }

  async getAllMockUps() {
    const formData = new FormData()
    formData.append(window.csrfTokenName, window.csrfTokenValue)

    const allMockUps = await Axios.post(
      '/actions/print-plugin/front/get-all-pdf',
      formData, {headers: {'Content-Type': 'multipart/form-data'}})
      .then((response) => response.data)
      .catch((err) => console.log(err))

    return allMockUps
  }

  async getAllFiles() {
    const formData = new FormData()
    formData.append(window.csrfTokenName, window.csrfTokenValue)

    const allFiles = await Axios.post(
      '/actions/print-plugin/front/get-all-static',
      formData, {headers: {'Content-Type': 'multipart/form-data'}})
      .then((response) => response.data)
      .catch((err) => console.log(err))

    return allFiles
  }

  async downloadJPEG(mockUpId) {
    try {
      await fetchTemplateAndSettingsService(mockUpId)
        .then(({html = `<h1>Undefined template</h1>`, settings = {}}) => {
          this.PDFService.setOptions(settings)

          this.PDFService.createJPEG(html).then(jpeg => {
            const downloadLink = document.getElementById(`download-link-${mockUpId}`)
            downloadLink.href = jpeg
            downloadLink.click()
            return true
          })
        })
    } catch (e) {
      console.log('Error!!! ', e)
    }
  }

  async downloadPDF(mockUpId) {
    try {
      fetchTemplateAndSettingsService(mockUpId)
        .then(({html = `<h1>Undefined template</h1>`, settings = {}}) => {
          this.PDFService.setOptions(settings)

          this.PDFService.createPDF(html)
        })
    } catch (e) {
      console.log('Error!!! ', e)
    }
  }

  async downloadHTML(mockUpId) {
    try {
      fetchTemplateAndSettingsService(mockUpId)
        .then(({html = `<h1>Undefined template</h1>`}) => {
          const downloadLink = document.getElementById(`download-link-${mockUpId}`)

          downloadLink.setAttribute('href', 'data:text/plain;charset=utf-8,' +
            encodeURIComponent(html))

          downloadLink.click()
          return true
        })
    } catch (e) {
      console.log('Error!!! ', e)
    }
  }

  async getPreview(mockUpId) {
    const template = await fetchTemplateAndSettingsService(mockUpId)
      .then(({html = `<h1>Undefined template</h1>`, settings = {}}) => {
        this.PDFService.setOptions(settings)
        return html
      })

    const previewJPEG = await this.PDFService.createJPEG(template)

    return previewJPEG
  }
}

export default PrintPluginService