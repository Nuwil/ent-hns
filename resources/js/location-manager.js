/* Location Manager - Handles country, state, and city selection */

class LocationManager {
  constructor() {
    this.locationData = {};
    this.initialized = false;
    this.init();
  }

  async init() {
    try {
      const response = await fetch('/data/locations.json');
      const data = await response.json();
      this.locationData = data;
      this.initialized = true;
      this.setupListeners();
    } catch (error) {
      console.error('Failed to load locations data:', error);
    }
  }

  setupListeners() {
    // Setup for create modal
    this.setupFormListeners('create');
  }

  setupFormListeners(prefix) {
    const countryInput = document.getElementById(`${prefix}-country-search`);
    const countrySelect = document.getElementById(`${prefix}-country`);
    const stateInput = document.getElementById(`${prefix}-state-search`);
    const stateSelect = document.getElementById(`${prefix}-state`);
    const cityInput = document.getElementById(`${prefix}-city-search`);
    const citySelect = document.getElementById(`${prefix}-city`);

    if (countryInput && countrySelect) {
      // Initialize countries
      this.populateCountries(countrySelect);

      // Country search filtering
      countryInput.addEventListener('input', (e) => {
        this.filterOptions(countrySelect, e.target.value);
      });

      // Country selection
      countrySelect.addEventListener('change', (e) => {
        countryInput.value = '';
        stateInput.value = '';
        cityInput.value = '';
        stateSelect.innerHTML = '';
        citySelect.innerHTML = '';

        if (e.target.value) {
          this.populateStates(e.target.value, stateSelect);
        }
      });
    }

    if (stateInput && stateSelect) {
      // State search filtering
      stateInput.addEventListener('input', (e) => {
        this.filterOptions(stateSelect, e.target.value);
      });

      // State selection
      stateSelect.addEventListener('change', (e) => {
        stateInput.value = '';
        cityInput.value = '';
        citySelect.innerHTML = '';

        if (countrySelect.value && e.target.value) {
          this.populateCities(countrySelect.value, e.target.value, citySelect);
        }
      });
    }

    if (cityInput && citySelect) {
      // City search filtering
      cityInput.addEventListener('input', (e) => {
        this.filterOptions(citySelect, e.target.value);
      });

      // City selection
      citySelect.addEventListener('change', (e) => {
        cityInput.value = '';
      });
    }
  }

  populateCountries(selectElement) {
    if (!this.locationData.countries || !Array.isArray(this.locationData.countries)) {
      return;
    }

    selectElement.innerHTML = '<option value="">Select Country</option>';
    this.locationData.countries.forEach((country) => {
      const option = document.createElement('option');
      option.value = country;
      option.textContent = country;
      selectElement.appendChild(option);
    });
  }

  populateStates(country, selectElement) {
    // For now, populate with a placeholder until we have state data
    // In future, this would be populated from the CSV data
    selectElement.innerHTML = '<option value="">Select State/Province</option>';

    // Example states for common countries - in production this would come from a complete data source
    const stateData = {
      'United States': [
        'Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware',
        'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky',
        'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi',
        'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico',
        'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania',
        'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont',
        'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming'
      ],
      'Canada': [
        'Alberta', 'British Columbia', 'Manitoba', 'New Brunswick', 'Newfoundland and Labrador',
        'Northwest Territories', 'Nova Scotia', 'Nunavut', 'Ontario', 'Prince Edward Island',
        'Quebec', 'Saskatchewan', 'Yukon'
      ],
      'India': [
        'Andaman and Nicobar Islands', 'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar',
        'Chandigarh', 'Chhattisgarh', 'Dadra and Nagar Haveli', 'Daman and Diu', 'Delhi', 'Goa',
        'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand', 'Karnataka', 'Kerala', 'Lakshadweep',
        'Madhya Pradesh', 'Maharashtra', 'Manipur', 'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha',
        'Puducherry', 'Punjab', 'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura',
        'Uttar Pradesh', 'Uttarakhand', 'West Bengal'
      ],
      'Australia': [
        'New South Wales', 'Queensland', 'South Australia', 'Tasmania', 'Victoria', 'Western Australia'
      ],
      'Brazil': [
        'Acre', 'Alagoas', 'Amapá', 'Amazonas', 'Bahia', 'Ceará', 'Distrito Federal', 'Espírito Santo',
        'Goiás', 'Maranhão', 'Mato Grosso', 'Mato Grosso do Sul', 'Minas Gerais', 'Pará', 'Paraíba',
        'Paraná', 'Pernambuco', 'Piauí', 'Rio de Janeiro', 'Rio Grande do Norte', 'Rio Grande do Sul',
        'Rondônia', 'Roraima', 'Santa Catarina', 'São Paulo', 'Sergipe', 'Tocantins'
      ],
      'Mexico': [
        'Aguascalientes', 'Baja California', 'Baja California Sur', 'Campeche', 'Chiapas', 'Chihuahua',
        'Ciudad de México', 'Coahuila', 'Colima', 'Durango', 'Guanajuato', 'Guerrero', 'Hidalgo',
        'Jalisco', 'Michoacán', 'Morelos', 'Nayarit', 'Nuevo León', 'Oaxaca', 'Puebla', 'Querétaro',
        'Quintana Roo', 'San Luis Potosí', 'Sinaloa', 'Sonora', 'Tabasco', 'Tamaulipas', 'Tlaxcala',
        'Veracruz', 'Yucatán', 'Zacatecas'
      ],
      'Philippines': [
        'Abra', 'Agusan del Norte', 'Agusan del Sur', 'Aklan', 'Albay', 'Antique', 'Apayao',
        'Aurora', 'Basilan', 'Bataan', 'Batangas', 'Benguet', 'Biliran', 'Bohol', 'Bukidnon',
        'Bulacan', 'Calimantan', 'Camarines Norte', 'Camarines Sur', 'Camiguin', 'Capiz', 'Catanduanes',
        'Cavite', 'Cebu', 'Cotabato', 'Davao del Norte', 'Davao del Sur', 'Davao Oriental', 'Dinagat Islands',
        'Eastern Samar', 'Guimaras', 'Ifugao', 'Ilocos Norte', 'Ilocos Sur', 'Iloilo', 'Isabela',
        'Kalinga', 'Lanao del Norte', 'Lanao del Sur', 'La Union', 'Laguna', 'Leyte', 'Maguindanao',
        'Marinduque', 'Masbate', 'Metro Manila', 'Misamis Occidental', 'Misamis Oriental', 'Mountain Province',
        'Negros Occidental', 'Negros Oriental', 'Northern Samar', 'Nueva Ecija', 'Nueva Vizcaya', 'Palawan',
        'Pampanga', 'Pangasinan', 'Quezon', 'Quirino', 'Rizal', 'Romblon', 'Samar', 'Sarangani',
        'Siquijor', 'Sorsogon', 'South Cotabato', 'Southern Leyte', 'Surigao del Norte', 'Surigao del Sur',
        'Tarlac', 'Tawi-Tawi', 'Zambales', 'Zamboanga del Norte', 'Zamboanga del Sur', 'Zamboanga Sibugay'
      ]
    };

    if (stateData[country]) {
      stateData[country].forEach((state) => {
        const option = document.createElement('option');
        option.value = state;
        option.textContent = state;
        selectElement.appendChild(option);
      });
    }
  }

  populateCities(country, state, selectElement) {
    // Placeholder cities data - in production this would come from the complete CSV
    selectElement.innerHTML = '<option value="">Select City</option>';

    // Example cities for common country/state combinations
    const cityData = {
      'United States|California': ['Los Angeles', 'San Francisco', 'San Diego', 'Sacramento', 'Long Beach'],
      'United States|New York': ['New York', 'Buffalo', 'Rochester', 'Yonkers', 'Syracuse'],
      'United States|Texas': ['Houston', 'Dallas', 'Austin', 'San Antonio', 'Fort Worth'],
      'Canada|Ontario': ['Toronto', 'Ottawa', 'Hamilton', 'London', 'Mississauga'],
      'Canada|Quebec': ['Montreal', 'Quebec City', 'Laval', 'Gatineau', 'Longueuil'],
      'India|Maharashtra': ['Mumbai', 'Pune', 'Nagpur', 'Aurangabad', 'Solapur'],
      'India|Delhi': ['New Delhi', 'Delhi'],
      'India|Karnataka': ['Bangalore', 'Belagavi', 'Hubballi', 'Mangalore', 'Kalaburagi'],
      'Brazil|São Paulo': ['São Paulo', 'Guarulhos', 'Campinas', 'São Bernardo do Campo', 'Sorocaba'],
      'Brazil|Rio de Janeiro': ['Rio de Janeiro', 'Niterói', 'Duque de Caxias', 'San Gonçalo', 'Itaboraí'],
      'Philippines|Metro Manila': ['Manila', 'Makati', 'Quezon City', 'Pasig', 'Taguig'],
      'Philippines|Cebu': ['Cebu City', 'Mandaue', 'Lapulapu', 'Talisay', 'Danao']
    };

    const key = `${country}|${state}`;
    if (cityData[key]) {
      cityData[key].forEach((city) => {
        const option = document.createElement('option');
        option.value = city;
        option.textContent = city;
        selectElement.appendChild(option);
      });
    }
  }

  filterOptions(selectElement, searchText) {
    const lowerSearch = searchText.toLowerCase();
    const options = selectElement.querySelectorAll('option');

    options.forEach((option) => {
      if (option.value === '') {
        option.style.display = '';
      } else {
        const matches = option.textContent.toLowerCase().includes(lowerSearch);
        option.style.display = matches ? '' : 'none';
      }
    });
  }
}

// Initialize and expose to global scope
let LocationsManager;
document.addEventListener('DOMContentLoaded', function() {
  LocationsManager = new LocationManager();
});

// Also expose the class itself
if (typeof window !== 'undefined') {
  window.LocationManager = LocationManager;
  window.LocationsManager = LocationsManager;
}

export default LocationManager;
