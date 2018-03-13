{
    var editor = new MediumEditor('.editable', {
        delay: 1000,
        targetBlank: true,
        toolbar: {
            buttons: ['bold', 'italic', 'quote', 'pre','anchor','indent','outdent','h1','h2','h3','orderedlist','unorderedlist','justifyLeft','justifyCenter','justifyRight'],
            diffLeft: 25,
            diffTop: 10,
        },
        anchor: {
            placeholderText: 'Type a link',
            customClassOption: 'btn',
            customClassOptionText: 'Create Button'
        },
        paste: {
            cleanPastedHTML: true,
            cleanAttrs: ['style', 'dir'],
            cleanTags: ['label', 'meta'],
            unwrapTags: ['sub', 'sup']
        },
        anchorPreview: {
            hideDelay: 300
        },
        placeholder: {
            text: 'Click to edit'
        }
    });
}