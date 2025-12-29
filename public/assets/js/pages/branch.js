document.addEventListener('DOMContentLoaded', () => {
    const rows = [...document.querySelectorAll('#branchTable tbody tr')]
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
            r.querySelector('.branch-name').innerText.toLowerCase().includes(keyword)
        )

        const totalPages = Math.max(1, Math.ceil(filtered.length / limit))
        page = Math.min(page, totalPages)

        rows.forEach(r => r.style.display = 'none')

        const start = (page - 1) * limit
        const end = start + limit
        filtered.slice(start, end).forEach(r => r.style.display = '')

        info.innerText = `Menampilkan ${filtered.length} data`
        pageInfo.innerText = `${page} / ${totalPages}`

        prevBtn.disabled = page === 1
        nextBtn.disabled = page === totalPages
    }

    searchInput.addEventListener('input', () => { page = 1; render() })
    limitSelect.addEventListener('change', () => { page = 1; render() })
    prevBtn.addEventListener('click', () => { page--; render() })
    nextBtn.addEventListener('click', () => { page++; render() })

    render()
})
