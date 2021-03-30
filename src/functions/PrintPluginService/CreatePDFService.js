import html2canvas from 'html2canvas'

export default class CreatePDFService {
  constructor() {
    this._options = {
      orientation: 'portrait', // "portrait" or "landscape"
      unit: 'cm', // "mm", "cm", "m", "in" or "px"
      format: 'a4', // a0 - a10
    }
  }

  setOptions(options) {
    if (options.format === 'cm' || options.format === 'px') {
      this._options = {
        unit: options.format,
        orientation: +JSON.parse(options.size).width < +JSON.parse(options.size).height
          ? 'portrait' : 'landscape',
        format: [
          +JSON.parse(options.size).width,
          +JSON.parse(options.size).height,
        ]
      }
    } else {
      this._options = {
        format: JSON.parse(options.size).format
      }
    }
  }

  async createPDF(template) {
    const printContainer = document.querySelector('.pdf-container-by-print')
    printContainer.innerHTML = template.trim()

    const options = {
      useCORS: true,
      allowTaint: true,
      scrollY: window.scrollY,
      y: window.scrollY + 147
    }

    await html2canvas(printContainer, options).then(canvas => {
      const imgData = canvas.toDataURL("image/jpeg",1)
      const pdf = new window.jspdf.jsPDF(this._options)
      const pageWidth = pdf.internal.pageSize.getWidth()
      const pageHeight = pdf.internal.pageSize.getHeight()
      const imageWidth = canvas.width
      const imageHeight = canvas.height

      const ratio = imageWidth/imageHeight >= pageWidth/pageHeight ? pageWidth/imageWidth : pageHeight/imageHeight
      pdf.addImage(imgData, 'JPEG', 0, 0, imageWidth * ratio, imageHeight * ratio)
      pdf.save("pdf-file.pdf")

      document.querySelector('.pdf-container-by-print').innerHTML = ''
    })
    return true
  }

  async createJPEG(template) {
    const printContainer = document.querySelector('.pdf-container-by-print')
    printContainer.innerHTML = template.trim()

    const options = {
      useCORS: true,
      allowTaint: true,
      scrollY: window.scrollY,
      y: window.scrollY + 147
    }

    const jpeg = await html2canvas(printContainer, options, options.width, options.height).then(canvas => {
      if (this._options.unit === 'px') {
        const extra_canvas = document.createElement("canvas")
        let factorWidth = 1
        let factorHeight = 1

        extra_canvas.setAttribute('width',this._options.format[0] * factorWidth)
        extra_canvas.setAttribute('height',this._options.format[1] * factorHeight)
        const ctx = extra_canvas.getContext('2d')
        ctx.drawImage(canvas,
          0, 0, canvas.width, canvas.height,
          0, 0, this._options.format[0] * factorWidth, this._options.format[1] * factorHeight
        )

        return extra_canvas.toDataURL("image/jpeg", 1)
      } else {
        return canvas.toDataURL("image/jpeg", 1)
      }
    })

    document.querySelector('.pdf-container-by-print').innerHTML = ''
    return jpeg
  }
}