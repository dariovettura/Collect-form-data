<?php
// Options page content
?>

<div class="wrap">
    <h1>Options</h1>
    <h2 class="nav-tab-wrapper">
        <a href="#configuration" class="nav-tab nav-tab-active" onclick="showTab(event, 'configuration')">Configuration</a>
        <a href="#blacklist" class="nav-tab" onclick="showTab(event, 'blacklist')">Blacklist</a>
    </h2>

    <!-- Configuration Tab Content -->
    <div id="configuration" class="tab-content active">
        <h2>Configuration</h2>
        <form method="post" action="options.php">
            <table class="form-table">
                <!-- Form ID Input -->
                <tr>
                    <th scope="row"><label for="form_id">Form ID</label></th>
                    <td>
                        <input type="text" id="form_id" name="form_id" value="" class="regular-text" placeholder="Enter form ID">
                    </td>
                </tr>
            </table>

            <h3>Map Form Fields</h3>
            <div id="field-mapping-container">
                <!-- Field Mapping Rows -->
                <div class="field-mapping-row">
                    <input type="text" name="form_field_ids[]" class="regular-text" placeholder="Input form ID (e.g., name_768)">
                    <span>→</span>
                    <input type="text" name="form_field_names[]" class="regular-text" placeholder="Save as name (e.g., name)">
                </div>
            </div>

            <p>
                <button type="button" class="button" id="add-mapping-row">Add Field Mapping</button>
            </p>

            <p>
                <input type="submit" class="button-primary" value="Save Configuration">
            </p>
        </form>
    </div>

    <!-- Blacklist Tab Content -->
    <div id="blacklist" class="tab-content">
        <h2>Blacklist</h2>
        <p>Manage the list of blacklisted items.</p>
    </div>
</div>

<!-- JavaScript to Handle Dynamic Field Mapping -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const mappingContainer = document.getElementById('field-mapping-container');
    const addMappingRowButton = document.getElementById('add-mapping-row');

    addMappingRowButton.addEventListener('click', () => {
        // Create a new row for field mapping
        const row = document.createElement('div');
        row.classList.add('field-mapping-row');
        
        // Input for form field ID
        const inputFieldID = document.createElement('input');
        inputFieldID.type = 'text';
        inputFieldID.name = 'form_field_ids[]';
        inputFieldID.className = 'regular-text';
        inputFieldID.placeholder = 'Input form ID (e.g., name_768)';
        
        // Separator (arrow)
        const arrow = document.createElement('span');
        arrow.textContent = '→';
        
        // Input for saving as name
        const inputFieldName = document.createElement('input');
        inputFieldName.type = 'text';
        inputFieldName.name = 'form_field_names[]';
        inputFieldName.className = 'regular-text';
        inputFieldName.placeholder = 'Save as name (e.g., name)';
        
        // Append inputs and arrow to the row
        row.appendChild(inputFieldID);
        row.appendChild(arrow);
        row.appendChild(inputFieldName);
        
        // Add the row to the container
        mappingContainer.appendChild(row);
    });
});

// JavaScript to handle tabs remains unchanged
function showTab(event, tabId) {
    event.preventDefault();

    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(tab => tab.style.display = 'none');

    const tabs = document.querySelectorAll('.nav-tab');
    tabs.forEach(tab => tab.classList.remove('nav-tab-active'));

    document.getElementById(tabId).style.display = 'block';
    event.target.classList.add('nav-tab-active');
}
</script>

<!-- CSS for styling -->
<style>
.tab-content {
    display: none;
    margin-top: 20px;
}
.tab-content.active {
    display: block;
}
.field-mapping-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}
button#add-mapping-row {
    margin-top: 10px;
}
</style>
