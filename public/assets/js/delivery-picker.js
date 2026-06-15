document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delivery-picker').forEach(initDeliveryPicker);
});

function initDeliveryPicker(root) {
    const prefix = root.dataset.prefix;
    const carrierInput = document.getElementById(prefix + '_carrier');
    const cityRefInput = document.getElementById(prefix + '_city_ref');
    const branchRefInput = document.getElementById(prefix + '_branch_ref');
    const cityHidden = document.getElementById(prefix + '_city_hidden');
    const addressHidden = document.getElementById(prefix + '_branch_hidden') || document.getElementById(prefix + '_address_hidden');
    const methodInput = document.getElementById(prefix + '_method');
    const manualInput = document.getElementById(prefix + '_manual');
    const autoBlock = document.getElementById(prefix + '_auto_block');
    const manualBlock = document.getElementById(prefix + '_manual_block');
    const manualLink = document.getElementById(prefix + '_manual_link');
    const autoLink = document.getElementById(prefix + '_auto_link');
    const citySearch = document.getElementById(prefix + '_city_search');
    const pointSearch = document.getElementById(prefix + '_point_search');
    const citySuggest = document.getElementById(prefix + '_city_suggest');
    const pointSuggest = document.getElementById(prefix + '_point_suggest');
    const selectedBox = document.getElementById(prefix + '_selected');
    const pointHint = document.getElementById(prefix + '_point_hint');
    const apiNote = document.getElementById(prefix + '_api_note');
    const manualCity = document.getElementById(prefix + '_manual_city');
    const manualAddress = document.getElementById(prefix + '_manual_address');

    let carrier = root.dataset.savedCarrier || 'nova_poshta';
    let cityRef = root.dataset.savedCityRef || '';
    let cityName = root.dataset.savedCity || '';
    let pointName = root.dataset.savedBranch || '';
    let pointRef = root.dataset.savedBranchRef || '';
    let cityTimer = null;
    let pointTimer = null;
    let manualMode = root.dataset.manual === '1';

    const defaultEntry = { city: '', city_ref: '', branch: '', branch_ref: '', manual: false };
    let carrierStore = {};

    try {
        carrierStore = JSON.parse(root.dataset.savedAll || '{}') || {};
    } catch (e) {
        carrierStore = {};
    }

    function getStoreEntry(c) {
        return Object.assign({}, defaultEntry, carrierStore[c] || {});
    }

    function saveCurrentToStore() {
        syncHidden();
        carrierStore[carrier] = {
            city: isManualMode() ? manualCity.value.trim() : cityName,
            city_ref: isManualMode() ? '' : cityRef,
            branch: isManualMode() ? manualAddress.value.trim() : pointName,
            branch_ref: isManualMode() ? '' : pointRef,
            manual: isManualMode(),
        };
    }

    function loadFromStore(c) {
        const entry = getStoreEntry(c);
        manualMode = !!entry.manual || c === 'courier' || c === 'pickup';
        cityRef = entry.city_ref || '';
        cityName = entry.city || '';
        pointName = entry.branch || '';
        pointRef = entry.branch_ref || '';
        citySearch.value = entry.city || '';
        pointSearch.value = entry.branch || '';
        manualCity.value = entry.city || '';
        manualAddress.value = entry.branch || '';
        pointSearch.disabled = !cityRef && !manualMode && postCarriers.includes(c);
        if (pointHint) {
            pointHint.style.display = cityRef && !pointName && postCarriers.includes(c) ? 'block' : 'none';
        }
        closeSuggests(null);
    }

    const postCarriers = ['nova_poshta', 'ukrposhta', 'meest'];

    const carrierNotes = {
        nova_poshta: 'Обери місто зі списку, потім відділення або поштомат.',
        ukrposhta: 'Обери місто зі списку, потім відділення Укрпошти.',
        meest: 'Обери місто зі списку, потім відділення Meest.',
        courier: 'Курʼєрська доставка — вкажи повну адресу вручну.',
        pickup: 'Самовивіз з магазину ClothStore.',
    };

    function isManualMode() {
        return manualMode || carrier === 'courier' || carrier === 'pickup';
    }

    function syncHidden() {
        carrierInput.value = carrier;
        if (methodInput) methodInput.value = carrier;
        cityRefInput.value = cityRef;
        branchRefInput.value = pointRef;
        manualInput.value = isManualMode() ? '1' : '0';

        if (isManualMode()) {
            const city = manualCity.value.trim();
            const addr = manualAddress.value.trim();
            cityHidden.value = city;
            if (addressHidden) addressHidden.value = addr;
            if (selectedBox) {
                selectedBox.innerHTML = city && addr ? '<strong>Обрано:</strong> ' + city + ', ' + addr : '';
            }
            return;
        }

        cityHidden.value = cityName;
        if (addressHidden) addressHidden.value = pointName;
        if (selectedBox) {
            selectedBox.innerHTML = cityName && pointName
                ? '<strong>Обрано:</strong> ' + cityName + ', ' + pointName
                : '';
        }
    }

    function toggleMode(forceManual) {
        if (typeof forceManual === 'boolean') {
            manualMode = forceManual;
        }

        const manual = isManualMode();
        autoBlock.style.display = manual ? 'none' : 'block';
        manualBlock.style.display = manual ? 'block' : 'none';
        apiNote.textContent = carrierNotes[carrier] || '';
        syncHidden();
    }

    function setCarrier(next) {
        if (next !== carrier) {
            saveCurrentToStore();
        }

        carrier = next;
        root.querySelectorAll('.delivery-carrier-tab').forEach(function (btn) {
            btn.classList.toggle('active', btn.dataset.carrier === next);
        });

        loadFromStore(next);

        if (postCarriers.includes(next) && !getStoreEntry(next).manual) {
            manualMode = false;
        } else if (!postCarriers.includes(next)) {
            manualMode = true;
        }

        toggleMode();
    }

    async function fetchJson(url) {
        const res = await fetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        });

        if (!res.ok) {
            throw new Error('HTTP ' + res.status);
        }

        return res.json();
    }

    function closeSuggests(except) {
        [citySuggest, pointSuggest].forEach(function (el) {
            if (el && el !== except) {
                el.classList.remove('open');
                el.innerHTML = '';
            }
        });
    }

    function renderSuggest(container, items, onPick) {
        closeSuggests(container);
        container.innerHTML = '';

        if (!items.length) {
            container.innerHTML = '<div class="delivery-suggest-empty">Нічого не знайдено</div>';
            container.classList.add('open');
            return;
        }

        items.forEach(function (item) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'delivery-suggest-item';
            btn.innerHTML = '<strong>' + escapeHtml(item.name) + '</strong>' +
                (item.area ? '<span>' + escapeHtml(item.area) + '</span>' : '') +
                (item.type ? '<small>' + escapeHtml(item.type) + '</small>' : '');
            btn.addEventListener('click', function () { onPick(item); });
            container.appendChild(btn);
        });
        container.classList.add('open');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    async function searchCities(q) {
        if (q.length < 2 || isManualMode() || !postCarriers.includes(carrier)) {
            citySuggest.classList.remove('open');
            return;
        }

        try {
            const data = await fetchJson('/api/delivery/cities?carrier=' + encodeURIComponent(carrier) + '&q=' + encodeURIComponent(q));
            renderSuggest(citySuggest, data.items || [], function (item) {
                cityRef = item.ref;
                cityName = item.name;
                citySearch.value = item.name;
                closeSuggests(null);
                pointSearch.disabled = false;
                pointSearch.value = '';
                pointName = '';
                pointRef = '';
                if (pointHint) pointHint.style.display = 'block';
                if (selectedBox) selectedBox.innerHTML = '';
                pointSearch.focus();
                syncHidden();
            });
        } catch (e) {
            citySuggest.innerHTML = '<div class="delivery-suggest-empty">Помилка завантаження списку</div>';
            citySuggest.classList.add('open');
        }
    }

    async function loadPoints(q) {
        if (!cityRef || isManualMode()) return;

        try {
            const data = await fetchJson('/api/delivery/points?carrier=' + encodeURIComponent(carrier) + '&city_ref=' + encodeURIComponent(cityRef) + '&q=' + encodeURIComponent(q || ''));
            renderSuggest(pointSuggest, data.items || [], function (item) {
                pointRef = item.ref;
                pointName = item.name + (item.short ? ' — ' + item.short : '');
                pointSearch.value = pointName;
                closeSuggests(null);
                if (pointHint) pointHint.style.display = 'none';
                syncHidden();
            });
        } catch (e) {
            pointSuggest.innerHTML = '<div class="delivery-suggest-empty">Помилка завантаження відділень</div>';
            pointSuggest.classList.add('open');
        }
    }

    root.querySelectorAll('.delivery-carrier-tab').forEach(function (btn) {
        btn.addEventListener('click', function () { setCarrier(btn.dataset.carrier); });
    });

    if (manualLink) {
        manualLink.addEventListener('click', function () {
            manualCity.value = citySearch.value.trim() || cityName;
            manualAddress.value = pointSearch.value.trim() || pointName;
            toggleMode(true);
        });
    }

    if (autoLink) {
        autoLink.addEventListener('click', function () {
            citySearch.value = manualCity.value.trim();
            cityName = citySearch.value;
            cityRef = '';
            pointRef = '';
            pointSearch.value = '';
            pointSearch.disabled = true;
            toggleMode(false);
            if (citySearch.value.length >= 2) {
                searchCities(citySearch.value.trim());
            }
        });
    }

    manualCity.addEventListener('input', syncHidden);
    manualAddress.addEventListener('input', syncHidden);

    citySearch.addEventListener('input', function () {
        clearTimeout(cityTimer);
        const q = citySearch.value.trim();
        cityName = q;
        cityRef = '';
        pointRef = '';
        pointSearch.value = '';
        pointSearch.disabled = true;
        if (pointHint) pointHint.style.display = 'none';
        closeSuggests(null);
        syncHidden();
        cityTimer = setTimeout(function () { searchCities(q); }, 250);
    });

    citySearch.addEventListener('focus', function () {
        closeSuggests(citySuggest);
        const q = citySearch.value.trim();
        if (q.length >= 2) {
            searchCities(q);
        }
    });

    pointSearch.addEventListener('input', function () {
        clearTimeout(pointTimer);
        const q = pointSearch.value.trim();
        pointName = q;
        pointRef = '';
        syncHidden();
        if (!cityRef) {
            closeSuggests(null);
            return;
        }
        if (q.length < 1) {
            closeSuggests(null);
            return;
        }
        pointTimer = setTimeout(function () { loadPoints(q); }, 250);
    });

    pointSearch.addEventListener('focus', function () {
        closeSuggests(pointSuggest);
        if (!cityRef) return;
        const q = pointSearch.value.trim();
        if (q.length >= 1) {
            loadPoints(q);
        }
    });

    document.addEventListener('click', function (e) {
        if (!root.contains(e.target)) {
            closeSuggests(null);
        }
    });

    setCarrier(carrier);

    if (cityRef && !isManualMode()) {
        pointSearch.disabled = false;
    }

    if (!isManualMode() && citySearch.value.trim().length >= 2 && !cityRef) {
        searchCities(citySearch.value.trim());
    }

    syncHidden();
}
