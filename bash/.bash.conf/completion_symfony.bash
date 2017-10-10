_available_commands()
{
    bin/console list --raw | awk '{print $1}'
}
 
_symfony()
{
    local cur
    cur="${COMP_WORDS[COMP_CWORD]}"
    prev=${COMP_WORDS[COMP_CWORD-1]}
 
    if [ $COMP_CWORD -eq 1 ]
    then
        COMPREPLY=( $( compgen -W '$(_available_commands)' -- $cur) )
    fi
}
 
 
complete -F _symfony bin/console
COMP_WORDBREAKS=${COMP_WORDBREAKS//:}