import { fetchTemplateAndSettingsService, fetchNewTemplate } from './fetchTemplateAndSettingsService'
import fixPreviewSize from './fixPreviewSize'

document.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('template-redactor-view')) {
    const viewContainer = document.getElementById('template-redactor-view')
    const allFieldSelects = [...document.getElementsByTagName('select')]

    const onSelectInput = async (checkedSelect) => {
      if (checkedSelect.value === 'custom') {
        const selectName = checkedSelect.dataset.name

        checkedSelect.closest('.fieldset').querySelector('textarea')
        .classList.add('--active')
        checkedSelect.closest('.fieldset').querySelector('textarea')
          .value = textareaValue(selectName)
      } else {
        document.getElementById(`${ checkedSelect.dataset.name }-textarea`)
        .classList.remove('--active')

        const id = document.getElementById('template-redactor-view').dataset.id
        const outputs = [...document.getElementsByTagName('select')].reduce((acc, select) => {
          acc[select.dataset.name] = select.value
          return acc
        }, {})

        const { html } = await fetchNewTemplate(
          `id=${ id }&outputs=` + JSON.stringify(outputs)
        )
        viewContainer.innerHTML = html;

        [...document.querySelectorAll('textarea.--active')].forEach(textarea => {
          const textareaName = textarea.dataset.name
          onTextareaInput(textareaName, textarea.value)
        })

        fixPreviewSize('template-redactor-view')
      }
    }

    const initRedactor = () => {
      [...document.getElementsByTagName('textarea')].forEach(textarea => {
        const name = textarea.dataset.name
        if (document.getElementById(name)) textarea.value = textareaValue(name)
        textarea.addEventListener('input', () =>
          onTextareaInput(name, textarea.value)
        )
      });

      allFieldSelects.forEach(select => {
        if (select.value === 'custom') {
          select.closest('.fieldset').querySelector('textarea')
          .classList.add('--active')
        }

        select.addEventListener('input', () => onSelectInput(select))
        }
      )
    }

    const getTemplate = async (id) => {
      const getTemplateAndSettings = await fetchTemplateAndSettingsService(id)
      return getTemplateAndSettings.html || {}
    }

    getTemplate(viewContainer.dataset.id).then((html) => {
      viewContainer.classList.add('--active')
      viewContainer.innerHTML = html

      fixPreviewSize('template-redactor-view')

      initRedactor()
    })

    const onTextareaInput = (name, value) => {
      if (document.querySelector(`[data-pp-field="${ name }"]`)) {
        const fieldContainer = document.querySelector(`[data-pp-field="${ name }"]`)
        fieldContainer.innerText = value
      } else if (document.querySelector(`[data-pp-background="${ name }"]`)) {
        document.querySelector(`[data-pp-background="${ name }"]`).style.backgroundImage = `url(${ value })`
      }
    }

    const textareaValue = (name) => {
      const value = document.querySelector(`[data-pp-field="${ name }"]`)?.innerText ||
        document.querySelector(`[data-pp-background="${ name }"]`)
        ?.style.backgroundImage
        ?.slice(5, -2)
      return value || ''
    }
  }
})