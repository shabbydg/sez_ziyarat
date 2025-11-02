<?php
// Nationality dropdown options
$nationalities = [
    'Afghan', 'Albanian', 'Algerian', 'American', 'Argentinian', 'Australian', 'Austrian',
    'Bahraini', 'Bangladeshi', 'Barbadian', 'Batswana', 'Belgian', 'Bolivian', 'Brazilian',
    'British', 'Bulgarian', 'Cambodian', 'Cameroonian', 'Canadian', 'Chilean', 'Chinese',
    'Colombian', 'Comoros', 'Costa Rican', 'Croatian', 'Cuban', 'Cyprus', 'Czech',
    'Danish', 'Deutsch', 'Dominican', 'Dutch', 'Ecuadorian', 'Egyptian', 'Emirati',
    'Estonian', 'Ethiopian', 'Fijian', 'Finnish', 'French', 'German', 'Ghanaian',
    'Greek', 'Guatemalan', 'Haitian', 'Honduran', 'Hungarian', 'Icelandic', 'Indian',
    'Indonesian', 'Iranian', 'Irish', 'Israeli', 'Italian', 'Jamaican', 'Japanese',
    'Jordanian', 'Kenyan', 'Korean', 'Kuwaiti', 'Lao', 'Latvian', 'Lebanese',
    'Libyan', 'Lithuanian', 'Luxembourg', 'Malagasy', 'Malaysian', 'Malian', 'Maltese',
    'Mauritius', 'Mexican', 'Mongolian', 'Moroccan', 'Mozambican', 'Myanmar', 'Namibian',
    'Nederlandse', 'Nepalese', 'Nevis', 'New Zealand', 'Nicaraguan', 'Nigerian',
    'Norwegian', 'Omani', 'Pakistani', 'Panamanian', 'Paraguayan', 'Peruvian',
    'Philippine', 'Polish', 'Portuguese', 'Romanian', 'Russian', 'Salvadorian',
    'Saudi', 'Scottish', 'Senegalese', 'Serbian', 'Singaporean', 'Slovak',
    'South African', 'South Korean', 'Spanish', 'Sri Lankan', 'St Kitts', 'Sudanese',
    'Swedish', 'Swiss', 'Syrian', 'Taiwanese', 'Tajikistani', 'Tanzanian', 'Thai',
    'Tongan', 'Tunisian', 'Turkish', 'Ugandan', 'Ukrainian', 'Uruguayan',
    'Venezuelan', 'Vietnamese', 'Welsh', 'Yemeni', 'Zambian', 'Zimbabwean'
];

function generateNationalityOptions($selected_nationality = '') {
    global $nationalities;
    $options = '<option value="">Nationality *</option>';
    foreach($nationalities as $nationality) {
        $selected = (strtolower($selected_nationality) == strtolower($nationality)) ? 'selected' : '';
        $options .= '<option value="' . $nationality . '" ' . $selected . '>' . $nationality . '</option>';
    }
    return $options;
}
?>
