<?PHP


function ctype_alsc($str)
{
    return ctype_alpha(str_replace('_', '', $str));
}

function ctype_aldotsc($str)
{
    return ctype_alpha(str_replace('_', '', str_replace('.', '', $str)));
}
