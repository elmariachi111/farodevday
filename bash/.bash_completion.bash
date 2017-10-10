## Added because old bash-completion versions doesn't have the
## _init_completion() function

# Initialize completion and deal with various general things: do file
# and variable completion where appropriate, and adjust prev, words,
# and cword as if no redirections exist so that completions do not
# need to deal with them.  Before calling this function, make sure
# cur, prev, words, and cword are local, ditto split if you use -s.
#
# Options:
#     -n EXCLUDE  Passed to _get_comp_words_by_ref -n with redirection chars
#     -e XSPEC    Passed to _filedir as first arg for stderr redirections
#     -o XSPEC    Passed to _filedir as first arg for other output redirections
#     -i XSPEC    Passed to _filedir as first arg for stdin redirections
#     -s          Split long options with _split_longopt, implies -n =
# @return  True (0) if completion needs further processing,
#          False (> 0) no further processing is necessary.
#
_init_completion()
{
    local exclude flag outx errx inx OPTIND=1

    while getopts "n:e:o:i:s" flag "$@"; do
        case $flag in
            n) exclude+=$OPTARG ;;
            e) errx=$OPTARG ;;
            o) outx=$OPTARG ;;
            i) inx=$OPTARG ;;
            s) split=false ; exclude+== ;;
        esac
    done

    # For some reason completion functions are not invoked at all by
    # bash (at least as of 4.1.7) after the command line contains an
    # ampersand so we don't get a chance to deal with redirections
    # containing them, but if we did, hopefully the below would also
    # do the right thing with them...

    COMPREPLY=()
    local redir="@(?([0-9])<|?([0-9&])>?(>)|>&)"
    _get_comp_words_by_ref -n "$exclude<>&" cur prev words cword

    # Complete variable names.
    if [[ $cur =~ ^(\$\{?)([A-Za-z0-9_]*)$ ]]; then
        [[ $cur == *{* ]] && local suffix=} || local suffix=
        COMPREPLY=( $( compgen -P ${BASH_REMATCH[1]} -S "$suffix" -v -- \
            "${BASH_REMATCH[2]}" ) )
        return 1
    fi

    # Complete on files if current is a redirect possibly followed by a
    # filename, e.g. ">foo", or previous is a "bare" redirect, e.g. ">".
    if [[ $cur == $redir* || $prev == $redir ]]; then
        local xspec
        case $cur in
            2'>'*) xspec=$errx ;;
            *'>'*) xspec=$outx ;;
            *'<'*) xspec=$inx ;;
            *)
                case $prev in
                    2'>'*) xspec=$errx ;;
                    *'>'*) xspec=$outx ;;
                    *'<'*) xspec=$inx ;;
                esac
                ;;
        esac
        cur="${cur##$redir}"
        _filedir $xspec
        return 1
    fi

    # Remove all redirections so completions don't have to deal with them.
    local i skip
    for (( i=1; i < ${#words[@]}; )); do
        if [[ ${words[i]} == $redir* ]]; then
            # If "bare" redirect, remove also the next word (skip=2).
            [[ ${words[i]} == $redir ]] && skip=2 || skip=1
            words=( "${words[@]:0:i}" "${words[@]:i+skip}" )
            [[ $i -le $cword ]] && cword=$(( cword - skip ))
        else
            i=$(( ++i ))
        fi
    done

    [[ $cword -eq 0 ]] && return 1
    prev=${words[cword-1]}

    [[ $split ]] && _split_longopt && split=true

    return 0
}

source ~/.git-completion.bash
source ~/.composer-bash-completion/composer