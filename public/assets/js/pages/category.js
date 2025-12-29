document.addEventListener('DOMContentLoaded', () => {
    const rows = [...document.querySelectorAll('#categoryTable tbody tr')]
    const searchInput = document.getElementById('searchInput')
    const limitSelect = document.getElementById('limitSelect')
    const info = document.getElementById('tableInfo')
    const pageInfo = document.getElementById('pageInfo')
    const prevBtn = document.getElementById('prevBtn')
    const nextBtn = document.getElementById('nextBtn')

    let page = 1

    function render() {
        const keyword = searchInput.value.toLowerCase()
        const limit = parseInt(limitSelect.value)
        const filtered = rows.filter(r =>
            r.querySelector('.category-name').innerText.toLowerCase().includes(keyword)
        )

        const totalPages = Math.ceil(filtered.length / limit)
        page = Math.min(page, totalPages || 1)

        rows.forEach(r => r.style.display = 'none')

        const start = (page - 1) * limit
        const end = start + limit
        filtered.slice(start, end).forEach(r => r.style.display = '')

        info.innerText = `Menampilkan ${filtered.length} data`
        pageInfo.innerText = `${page} / ${totalPages || 1}`

        prevBtn.disabled = page === 1
        nextBtn.disabled = page === totalPages
    }

    searchInput.addEventListener('input', () => { page = 1; render() })
    limitSelect.addEventListener('change', () => { page = 1; render() })
    prevBtn.addEventListener('click', () => { page--; render() })
    nextBtn.addEventListener('click', () => { page++; render() })

    render()
})
