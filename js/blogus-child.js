document.addEventListener(
    "DOMContentLoaded", () => {
        const terms = document.querySelectorAll(".books-terms-list__item");
        const output = document.querySelector("#books-list-wrapper");

        terms.forEach( (item) => {
            item.addEventListener('click',  async (event) =>  {
                event.preventDefault();

                const self = event.currentTarget;
                terms.forEach(item => item.classList.remove('active'))
                self.classList.add('active');

                const data = new FormData();
                data.append( 'action', 'blogus_book_filter' );
                data.append( 'nonce', blogus_book_filter.nonce );
                data.append( 'term', self.dataset.slug);

                try {
                    const response = await fetch(blogus_book_filter.url, {
                        method: 'POST',
                        credentials: 'same-origin',
                        body: data
                    });

                    const result = await response.json();

                    if (result.status === 'success') {
                        output.innerHTML = result.content;
                    }
                } catch (err) {
                    console.error(err);
                }
            });
        })
    },
);
