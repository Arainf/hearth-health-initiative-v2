import { Editor, Node } from '@tiptap/core'
import StarterKit from '@tiptap/starter-kit'

// Extensions
import Underline from '@tiptap/extension-underline'
import Strike from '@tiptap/extension-strike'
import CodeBlock from '@tiptap/extension-code-block'
import Link from '@tiptap/extension-link'
import Highlight from '@tiptap/extension-highlight'
import TextAlign from '@tiptap/extension-text-align'
import Superscript from '@tiptap/extension-superscript'
import Subscript from '@tiptap/extension-subscript'
import Image from '@tiptap/extension-image'
import { TableKit } from '@tiptap/extension-table'
import {FontSize, TextStyle} from "@tiptap/extension-text-style";

/* ==============================
   Two Column Node
================================ */
export const TwoColumn = Node.create({
    name: 'twoColumn',
    group: 'block',
    content: 'block+',
    draggable: true,

    parseHTML() {
        return [{ tag: 'two-column' }]
    },

    renderHTML() {
        return [
            'two-column',
            { class: 'grid grid-cols-2 gap-6 my-4' },
            0,
        ]
    },
})

/* ==============================
   Page Config
================================ */
const PAGE_HEIGHT = 1122 // A4 @ ~96dpi

/* ==============================
   Factory Function
================================ */
export function createTiptapEditor({
                                       element,
                                       content = '',
                                       editable = false,
                                   }) {
    if (!element) {
        throw new Error('TipTap editor element is required')
    }

    const editor = new Editor({
        element,
        content,
        editable,
        extensions: [
            StarterKit,
            Underline,
            Strike,
            CodeBlock,
            Highlight,
            Superscript,
            Subscript,
            Image,
            TextStyle,
            FontSize,
            TwoColumn,
            Link.configure({ openOnClick: false }),
            TextAlign.configure({
                types: ['heading', 'paragraph'],
            }),
        ],

    })

    /* ==============================
       Public API
    ================================ */
    return {
        editor,

        setEditable(value) {
            editor.setEditable(!!value)
        },

        setContent(html) {
            editor.commands.setContent(html, true)
        },

        getHTML() {
            return editor.getHTML()
        },


        /* Toolbar actions */
        undo() { editor.chain().focus().undo().run() },
        redo() { editor.chain().focus().redo().run() },

        canUndo() {
            return editor.can().undo()
        },

        canRedo() {
            return editor.can().redo()
        },

        toggleBold() { editor.chain().focus().toggleBold().run() },
        toggleItalic() { editor.chain().focus().toggleItalic().run() },
        toggleUnderline() { editor.chain().focus().toggleUnderline().run() },
        toggleStrike() { editor.chain().focus().toggleStrike().run() },

        toggleSize(size){ editor.chain().focus().setFontSize(`${size}`).run() },

        toggleHighlight() { editor.chain().focus().toggleHighlight().run() },
        toggleHeading(level) {
            editor.chain().focus().toggleHeading({ level }).run()
        },

        toggleBulletList() {
            editor.chain().focus().toggleBulletList().run()
        },

        toggleOrderedList() {
            editor.chain().focus().toggleOrderedList().run()
        },

        setAlign(align) {
            editor.chain().focus().setTextAlign(align).run()
        },


        isActive(type, opts = {}) {
           return  editor.isActive(type, opts)
        },
    }
}
