<?php
// Funzione per recuperare la configurazione salvata
function get_form_configuration() {
    $form_config = get_option('cfd_form_configuration');

    // Se non esiste, ritorna un array vuoto
    if (!$form_config) {
        return array(
            'form_id' => '',
            'field_mappings' => array()
        );
    }

    return $form_config;
}

$form_config = get_form_configuration();
$form_id = $form_config['form_id'];
$field_mappings = $form_config['field_mappings'];
?>

<form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
    <input type="hidden" name="action" value="save_configuration">
    <table class="form-table">
        <!-- Form ID Input -->
        <tr>
            <th scope="row"><label for="form_id">Form ID</label></th>
            <td>
                <input type="text" id="form_id" name="form_id" value="<?php echo esc_attr($form_id); ?>" class="regular-text" placeholder="Enter form ID">
            </td>
        </tr>
    </table>

    <h3>Map Form Fields</h3>
    <div id="field-mapping-container">
        <!-- Se ci sono mappings salvati, precompila il form con quelli -->
        <?php if (!empty($field_mappings)): ?>
            <?php foreach ($field_mappings as $mapping): ?>
                <div class="field-mapping-row">
                    <input type="text" name="form_field_ids[]" class="regular-text" value="<?php echo esc_attr($mapping['field_id']); ?>" placeholder="Input form ID (e.g., name_768)">
                    <span>→</span>
                    <input type="text" name="form_field_names[]" class="regular-text" value="<?php echo esc_attr($mapping['field_name']); ?>" placeholder="Save as name (e.g., name)">
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Riga vuota se non ci sono mappings salvati -->
            <div class="field-mapping-row">
                <input type="text" name="form_field_ids[]" class="regular-text" placeholder="Input form ID (e.g., name_768)">
                <span>→</span>
                <input type="text" name="form_field_names[]" class="regular-text" placeholder="Save as name (e.g., name)">
            </div>
        <?php endif; ?>
    </div>

    <p>
        <button type="button" class="button" id="add-mapping-row">Add Field Mapping</button>
    </p>

    <p>
        <input type="submit" class="button-primary" value="Save Configuration">
    </p>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const mappingContainer = document.getElementById('field-mapping-container');
    const addMappingRowButton = document.getElementById('add-mapping-row');

    addMappingRowButton.addEventListener('click', () => {
        // Crea una nuova riga per il mapping dei campi
        const row = document.createElement('div');
        row.classList.add('field-mapping-row');
        
        // Input per l'ID del campo
        const inputFieldID = document.createElement('input');
        inputFieldID.type = 'text';
        inputFieldID.name = 'form_field_ids[]';
        inputFieldID.className = 'regular-text';
        inputFieldID.placeholder = 'Input form ID (e.g., name_768)';
        
        // Separatore (freccia)
        const arrow = document.createElement('span');
        arrow.textContent = '→';
        
        // Input per il nome del campo da salvare
        const inputFieldName = document.createElement('input');
        inputFieldName.type = 'text';
        inputFieldName.name = 'form_field_names[]';
        inputFieldName.className = 'regular-text';
        inputFieldName.placeholder = 'Save as name (e.g., name)';
        
        // Aggiungi gli input e la freccia alla riga
        row.appendChild(inputFieldID);
        row.appendChild(arrow);
        row.appendChild(inputFieldName);
        
        // Aggiungi la riga al contenitore
        mappingContainer.appendChild(row);
    });
});

// JavaScript per la gestione delle tab
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
