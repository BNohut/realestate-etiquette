<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/@algolia/autocomplete-theme-classic"
/>

<div class="row justify-content-center">
    <div class="col-md-12 col-sm-12">
        <div id="autocomplete" class="mx-3"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/algoliasearch@4.20.0/dist/algoliasearch-lite.umd.js" integrity="sha256-DABVk+hYj0mdUzo+7ViJC6cwLahQIejFvC+my2M/wfM=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@algolia/autocomplete-js"></script>
<script>
</script>
<script>
    function findTag(recordType) {
        var tags = {
            "Alıcı Müşteri": "customer",
            "F.S.B.O.": "fsbo",
            "Çağrı": "call",
            "Pazarlama": "marketing",
            "Tapu Satış-Kiralama İşlemleri": "deed",
            "Yer Gösterme": "viewing",
            "Satış Kapama": "sale"
        };

        for (var key in tags) {
            if (recordType === key) {
                return tags[key];
            }
        }
        return "";
    }
    function turboLoadHandle(){
        const { autocomplete, getAlgoliaResults } = window['@algolia/autocomplete-js'];
        const appURL = '{{ env('APP_URL') }}';
        // Define Algolio Search Client (Application ID, API Admin Key)
        const searchClient = algoliasearch(
            'DQ66LJ8PMW',
            'c3ab0976ce624271dd1d3d505456b7ed'
        );

        // Define Debounce Function - Could be found in documentation
        function debouncePromise(fn, time) {
            let timerId = undefined;

            return function debounced(...args) {
                if (timerId) {
                clearTimeout(timerId);
                }

                return new Promise((resolve) => {
                timerId = setTimeout(() => resolve(fn(...args)), time);
                });
            };
        }

        const debounced = debouncePromise((items) => Promise.resolve(items), 1000);

        const algoliaEl = document.querySelector('#autocomplete div');

        if (algoliaEl) return;
        

        const algoliaWrapperEl = document.querySelector('#autocomplete');

        if (!algoliaWrapperEl) return;

        // Define Autocomplete
        const autocompleteSearch = autocomplete({
            container: '#autocomplete',
            placeholder: 'Portföy, kayıt, kişi, danışman arayın...',
            insights: true,
            getSources({ query }) {
                return debounced([
                {
                    sourceId: 'portfolios', // Could be used for recognition
                    getItems() {
                        return getAlgoliaResults({
                            searchClient,
                            queries: [
                            {
                                indexName: 'portfolios', // Algolia Index Name
                                query,
                                params: {
                                hitsPerPage: 3,
                                attributesToSnippet: ['title:10'],
                                snippetEllipsisText: '…',
                                },
                            },
                        
                            ],
                        });
                    },
                    // How to render the results
                    templates: {
                    header(item) {
                            return item.items[0].facet; // Facet shows us the model name
                    },
                    item({ item, components, html }) {
                        return html
                        `<div class="aa-ItemWrapper">
                            <a class="aa-ItemContent" href="${appURL}/admin/portfolio/${item.id}/detail">
                                <div class="aa-ItemContentBody">
                                    <div class="aa-ItemContentTitle">
                                        ${components.Highlight({
                                        hit: item,
                                        attribute: 'title',
                                        })}
                                    </div>
                                    <div class="aa-ItemContentDescription">
                                        ${components.Snippet({
                                        hit: item,
                                        attribute: 'province',
                                        })} /
                                        ${components.Snippet({
                                        hit: item,
                                        attribute: 'state',
                                        })} /
                                        ${components.Snippet({
                                        hit: item,
                                        attribute: 'neighborhood',
                                        })}
                                    </div>
                                </div>
                            </a>
                        </div>`;
                    },
                    },
                },
                // Second Index - Same Procces
                {
                    sourceId: 'records',
                    getItems() {
                        return getAlgoliaResults({
                            searchClient,
                            queries: [
                            {
                                indexName: 'records',
                                query,
                                params: {
                                hitsPerPage: 3,
                                attributesToSnippet: ['record_name:10'],
                                snippetEllipsisText: '…',
                                },
                            },
                        
                            ],
                        });
                    },
                    templates: {
                        header(item) {
                            return item.items[0].facet;
                        },
                        item({ item, components, html }) {
                            return html`<div class="aa-ItemWrapper">
                            <a class="aa-ItemContent" href="${appURL}/admin/${findTag(item.record_type)}/${item.id}/detail">
                                <div class="aa-ItemContentBody">
                                    <div class="aa-ItemContentTitle">
                                        ${components.Highlight({
                                        hit: item,
                                        attribute: 'record_type',
                                        })} Kaydı 
                                    </div>
                                    <div class="aa-ItemContentDescription">
                                        ${components.Snippet({
                                            hit: item,
                                            attribute: 'portfolio_title',
                                        })} 
                                    </div>
                                    <div class="aa-ItemContentDescription">
                                        Not: 
                                        ${components.Snippet({
                                            hit: item,
                                            attribute: 'notes',
                                        })}
                                    </div>
                                </div>
                
                            </a>
                            </div>`;
                        },
                    },
                },
                // Third Index - Same Procces
                {
                    sourceId: 'contacts',
                    getItems() {
                    return getAlgoliaResults({
                        searchClient,
                        queries: [
                        {
                            indexName: 'contacts',
                            query,
                            params: {
                            hitsPerPage: 3,
                            attributesToSnippet: ['name:10'],
                            snippetEllipsisText: '…',
                            },
                        },
                    
                        ],
                    });
                    },
                    templates: {
                        header(item) {
                            return item.items[0].facet;
                    },
                    item({ item, components, html }) {
                        return html
                        `<div class="aa-ItemWrapper">
                            <a class="aa-ItemContent" href="${appURL}/admin/contact/${item.id}/detail">
                                <div class="aa-ItemContentBody">
                                    <div class="aa-ItemContentTitle">
                                        ${components.Highlight({
                                        hit: item,
                                        attribute: 'name',
                                        })}
                                    </div>
                                    <div class="aa-ItemContentDescription">
                                        ${components.Snippet({
                                        hit: item,
                                        attribute: 'province',
                                        })}
                                    </div>
                                </div>
                            </a>
                        </div>`;
                    },
                    },
                },
                // Fourth Index - Same Procces
                {
                    sourceId: 'users',
                    getItems() {
                    return getAlgoliaResults({
                        searchClient,
                        queries: [
                        {
                            indexName: 'users',
                            query,
                            params: {
                            hitsPerPage: 3,
                            attributesToSnippet: ['full_name:10'],
                            snippetEllipsisText: '…',
                            },
                        },
                    
                        ],
                    });
                    },
                    templates: {
                        header(item) {
                            return item.items[0].facet;
                    },
                    item({ item, components, html }) {
                        return html
                        `<div class="aa-ItemWrapper">
                            <a class="aa-ItemContent" href="${appURL}/admin/consultant/${item.id}/detail">
                                <div class="aa-ItemContentBody">
                                    <div class="aa-ItemContentTitle">
                                        ${components.Highlight({
                                        hit: item,
                                        attribute: 'full_name',
                                        })}
                                    </div>
                                    <div class="aa-ItemContentDescription">
                                        ${components.Snippet({
                                        hit: item,
                                        attribute: 'email',
                                        })}
                                    </div>
                                </div>
                            </a>
                        </div>`;
                    },
                    },
                },
                ]);
            },
            // If No Results
            renderNoResults({ render, html, state }, root) {
                render(
                    html `
                        <div
                        style="padding: 0.5rem 1rem; color: #fff; font-size: 1.1rem"
                        >
                        No results for "${state.query}"
                        </div>
                    `,
                    root
                )
            },
        });
    }

    document.addEventListener('turbo:load', turboLoadHandle);

</script>


