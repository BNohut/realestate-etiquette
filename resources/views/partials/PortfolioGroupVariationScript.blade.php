@php
    use App\Models\Setting;
    $jsonData = json_decode(Setting::first()->config, true);
@endphp
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script>
        document.addEventListener("turbo:load", function () {
            let squareTotalLabel = $('#square-total').parent().parent().find('label').text('');
            squareTotalLabel.append($('<span>Brüt m</span><sup>2</sup>'));
            let squareNetLabel = $('#square-net').parent().parent().find('label').text('');
            squareNetLabel.append($('<span>Net m</span><sup>2</sup>'));

            //Catch Json Data Of Project
            let portfolioGroups = @json($jsonData)['portfolio_groups'];
            
            //Get Using Elements From Document By Using JQuery
            let portfolioGroupSelectEl = $('#portfolioGroup');
            let portfolioVariationSelectEl = $('#portfolioVariation');

            // When Portfolio Group Selected
            portfolioGroupSelectEl.on('input', function(){
                // Catch Variation SelectBox By Using Vanilla JS
                // We do this because of TomSelect
                // If we use the variable that we caught by using Jquery, We cant access tomselect.js
                let portfolioVariationSelectElement = document.getElementById('portfolioVariation');

                // Clear Variation SelectBox TomSelect Items
                portfolioVariationSelectElement.tomselect.clear();
                portfolioVariationSelectElement.tomselect.clearOptions();

                //Clear Variation SelectBox Options
                while (portfolioVariationSelectElement.firstChild) {
                    portfolioVariationSelectElement.removeChild(portfolioVariationSelectElement.firstChild);
                }
                //Catch Selected Portfolio Group Text
                let textOfSelection = $(this).find('option:selected').text();
                //Fetch Variations Of Selected Portfolio Group
                let variationsOfSelectedGroup = portfolioGroups[textOfSelection];
                const variationList = Object.keys(variationsOfSelectedGroup).map(key => ({ key: key, value: variationsOfSelectedGroup[key] }));
                variationList.forEach((variation, key) => {
                    portfolioVariationSelectEl.append($('<option></option>').attr('value', variation.key).text(variation.value));
                })
                // kodyazar dokunuşu
                // Take Render After Manipulation
                portfolioVariationSelectElement.tomselect.refreshItems();
                portfolioVariationSelectElement.tomselect.focus();
            })
        });
</script>