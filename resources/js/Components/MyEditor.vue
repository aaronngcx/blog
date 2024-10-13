<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import SectionBorder from "@/Components/SectionBorder.vue";
import { useEditor, EditorContent } from "@tiptap/vue-3";
import StarterKit from "@tiptap/starter-kit";
import Underline from "@tiptap/extension-underline"
import BoldIcon from 'vue-material-design-icons/FormatBold.vue'
import ItalicIcon from 'vue-material-design-icons/FormatItalic.vue'
import StrikeIcon from 'vue-material-design-icons/FormatStrikethrough.vue'
import UnderlineIcon from 'vue-material-design-icons/FormatUnderline.vue'
import ParagraphIcon from 'vue-material-design-icons/FormatParagraph.vue';
import H1Icon from 'vue-material-design-icons/FormatHeader1.vue';
import H2Icon from 'vue-material-design-icons/FormatHeader2.vue';
import H3Icon from 'vue-material-design-icons/FormatHeader3.vue';
import BulletListIcon from 'vue-material-design-icons/FormatListBulleted.vue';
import OrderedListIcon from 'vue-material-design-icons/FormatListNumbered.vue';
import CodeBlockIcon from 'vue-material-design-icons/CodeTags.vue';
import BlockquoteIcon from 'vue-material-design-icons/FormatQuoteClose.vue';
import HorizontalRuleIcon from 'vue-material-design-icons/Minus.vue';
import UndoIcon from 'vue-material-design-icons/UndoVariant.vue';
import RedoIcon from 'vue-material-design-icons/RedoVariant.vue';

const props = defineProps({
    modelValue: String,
})

const emit = defineEmits(['update:modelValue'])

const editor = useEditor({
    content: props.modelValue,
    onUpdate: ({editor}) => {
        emit('update:modelValue', editor.getHTML())
    },
    extensions: [StarterKit, Underline],
    editorProps: {
        attributes: {
            class: "border border-gray-400 p-4 min-h-[12rem] max-h-[12rem] overflow-y-auto bg-white outline-none tiptap prose max-w-none",
        },
        transformPastedText(text) {
            return text.toUpperCase();
        },
    },
});

</script>

<template>
    <div>
        <section v-if="editor" class="buttons flex items-center flex-wrap gap-x-4 bg-white border-gray-400 p-3 border-t border-l border-r tiptap">
            <button type="button" 
                @click="editor.chain().focus().toggleBold().run()" 
                :disabled="!editor.can().chain().focus().toggleBold().run()" 
                :class="{ 'bg-gray-200': editor.isActive('bold') }">
                <BoldIcon :size="24" title="Bold" />
            </button>
            <button type="button" 
                @click="editor.chain().focus().toggleItalic().run()" 
                :disabled="!editor.can().chain().focus().toggleItalic().run()" 
                :class="{ 'bg-gray-200': editor.isActive('italic') }">
                    <ItalicIcon :size="24" title="Italic" />
            </button>
            <button type="button" 
                @click="editor.chain().focus().toggleStrike().run()" 
                :disabled="!editor.can().chain().focus().toggleStrike().run()" 
                :class="{ 'bg-gray-200': editor.isActive('strike') }">
                    <StrikeIcon :size="24" title="Strike" />

            </button>
            <button type="button" 
                @click="editor.chain().focus().toggleUnderline().run()" 
                :disabled="!editor.can().chain().focus().toggleUnderline().run()" 
                :class="{ 'bg-gray-200': editor.isActive('underline') }">
                <UnderlineIcon :size="24" title="Underline" />
            </button>
            <button type="button" 
                @click="editor.chain().focus().toggleCode().run()" 
                :disabled="!editor.can().chain().focus().toggleCode().run()" 
                :class="{ 'bg-gray-200': editor.isActive('code') }">
                <CodeBlockIcon :size="24" title="Code Block" />
            </button>

            <button type="button" @click="editor.chain().focus().setParagraph().run()" :class="{ '': editor.isActive('paragraph') }">
                <ParagraphIcon :size="24" title="Paragraph" />
            </button>
            
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 1 }).run()" :class="{ 'bg-gray-200': editor.isActive('heading', { level: 1 }) }">
                <H1Icon :size="24" title="H1" />
            </button>
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()" :class="{ 'bg-gray-200': editor.isActive('heading', { level: 2 }) }">
                <H2Icon :size="24" title="H2" />
            </button>
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()" :class="{ 'bg-gray-200': editor.isActive('heading', { level: 3 }) }">
                <H3Icon :size="24" title="H3" />
            </button>
            <button type="button" @click="editor.chain().focus().toggleBulletList().run()" :class="{ 'bg-gray-200': editor.isActive('bulletList') }">
                <BulletListIcon :size="24" title="Bullet List" />
            </button>
            <button type="button" @click="editor.chain().focus().toggleOrderedList().run()" :class="{ 'bg-gray-200': editor.isActive('orderedList') }">
                <OrderedListIcon :size="24" title="Ordered List" />
            </button>
            <button type="button" @click="editor.chain().focus().toggleCodeBlock().run()" :class="{ 'bg-gray-200': editor.isActive('codeBlock') }">
                <CodeBlockIcon :size="24" title="Code Block" />
            </button>
            <button type="button" @click="editor.chain().focus().toggleBlockquote().run()" :class="{ 'bg-gray-200': editor.isActive('blockquote') }">
                <BlockquoteIcon :size="24" title="Block Quote" />
            </button>
            <button type="button" @click="editor.chain().focus().setHorizontalRule().run()">
                <HorizontalRuleIcon :size="24" title="Horizontal Rule" />
            </button>
            <button type="button" @click="editor.chain().focus().undo().run()" :disabled="!editor.can().chain().focus().undo().run()">
                <UndoIcon :size="24" title="Undo Icon" />
            </button>
            <button type="button" @click="editor.chain().focus().redo().run()" :disabled="!editor.can().chain().focus().redo().run()">
                <RedoIcon :size="24" title="Redo Icon" />
            </button>
        </section>
        <EditorContent :editor="editor" />
    </div>
</template>

<style>
.tiptap h1{
    font-size:32px;
    font-weight:bold
}

.tiptap h2{
    font-size:16;
    font-weight:bold
}

.tiptap h3{
    font-size:12;
    font-weight:bold
}
</style>
