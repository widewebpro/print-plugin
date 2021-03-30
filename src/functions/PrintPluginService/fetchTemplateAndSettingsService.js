export const fetchTemplateAndSettingsService = async (id) => {
  const templateAndSettings = await fetch(
    `/actions/print-plugin/default/get-html?id=${id}`
  )
    .then(response => response.text())
    .then(result => JSON.parse(result))
    .catch(error => console.error('error', error))

  return templateAndSettings
}

export const fetchNewTemplate = async (getParams) => {
  const template = await fetch(
    `/actions/print-plugin/default/get-html?${getParams}`
  )
    .then(response => response.text())
    .then(result => JSON.parse(result))
    .catch(error => console.error('error', error))

  return template
}