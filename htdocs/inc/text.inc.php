<?PHP

function ctype_aldotsc($str)
{
    return ctype_alpha(str_replace('_', '', str_replace('.', '', $str)));
}
