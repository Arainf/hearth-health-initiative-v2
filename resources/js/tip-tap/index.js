// import Alpine from 'alpinejs'
import { Editor } from '@tiptap/core'
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
import { FontSize, TextStyle } from "@tiptap/extension-text-style";



let tiptapEditor = null

document.addEventListener('alpine:init', () => {
    Alpine.data('tiptapEditor', (content = '') => {
        let editor = null

        return {
            init() {
                editor = new Editor({
                    element: this.$refs.editor,
                    content,
                    editable: false,
                    extensions: [
                        StarterKit,
                        Underline,
                        Strike,
                        CodeBlock,
                        Highlight,
                        Superscript,
                        Subscript,
                        Image,
                        FontSize,
                        TextStyle,
                        Link.configure({ openOnClick: false }),
                        TextAlign.configure({
                            types: ['heading', 'paragraph'],
                        }),
                    ],
                })

                // expose globally for doctor.js
                window.TipTap = {
                    setContent: html => editor.commands.setContent(html),
                    getHTML: () => editor.getHTML(),
                    setEditable: v => editor.setEditable(v),
                }
            },

            /* ===== Toolbar Actions ===== */
            undo() { editor.chain().focus().undo().run() },
            redo() { editor.chain().focus().redo().run() },

            toggleBold() { editor.chain().focus().toggleBold().run() },
            toggleItalic() { editor.chain().focus().toggleItalic().run() },
            toggleStrike() { editor.chain().focus().toggleStrike().run() },
            toggleUnderline() { editor.chain().focus().toggleUnderline().run() },


            toggleCode() { editor.chain().focus().toggleCode().run() },
            toggleCodeBlock() { editor.chain().focus().toggleCodeBlock().run() },

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
                return editor.isActive(type, opts)
            }
        }
    })
})

/* 🌉 SAFE BRIDGE (used by doctor.js) */
window.TipTap = {
    setEditable(value) {
        if (!tiptapEditor) return
        tiptapEditor.setEditable(!!value)
    },

    setContent(html) {
        if (!tiptapEditor) return
        tiptapEditor.commands.setContent(html, true)
    },

    getHTML() {
        return tiptapEditor ? tiptapEditor.getHTML() : ''
    }
}

// window.Alpine = Alpine
// Alpine.start()
