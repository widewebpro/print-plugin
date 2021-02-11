const fixPreviewSize = blockId => {
  const container = document.getElementById(blockId)
  const inner = container.firstChild

  const containerWidth = container.offsetWidth
  const innerWidth = inner.offsetWidth

  if (innerWidth > containerWidth) {
    const scale = containerWidth / innerWidth
    inner.style.transform = `scale(${ scale })`
  }
}

export default fixPreviewSize