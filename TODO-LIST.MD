Using the provided datasets, implement **searchable dropdown fields** for **Country, State/Province, and City** in the **Create Patient** and **Edit Patient Information** forms.

### Required Feature

Add a **dropdown with search functionality** for the following fields:

* Country
* State/Province
* City

These dropdowns must **load options from the provided datasets**.

### Behavior Requirements

1. **Country Dropdown**

   * Displays all available countries from the dataset.
   * Includes a **search input inside the dropdown** so users can quickly find a country.

2. **State/Province Dropdown**

   * Should update **based on the selected country**.
   * Only states/provinces belonging to the selected country should appear.

3. **City Dropdown**

   * Should update **based on the selected state/province**.
   * Only cities belonging to the selected state/province should appear.

### UI Requirements

* Use a **modern searchable dropdown component**.
* The dropdown should allow **typing to filter results**.
* Maintain consistent styling with the system’s UI design.

### Pages Where This Must Be Implemented

* Create Patient Modal/Form
* Edit Patient Profile Modal/Form

### Additional Requirement

Ensure that:

* Dropdown selections are **validated before form submission**.
* Previously saved values **automatically appear when editing a patient profile**.
