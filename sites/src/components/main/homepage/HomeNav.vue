<script setup lang="ts">
import { ref, inject } from 'vue';
import { useI18n } from 'vue-i18n'
import { onClickOutside } from '@vueuse/core'
import type { IPageData } from '@/components/library/interfaces';

const mobileNav = ref(false);
const target = ref(null);
const { t, locale } = useI18n({
  inheritLocale: true,
  useScope: 'local'
});

onClickOutside(target, (event) => mobileNav.value = false);
function openWindow(url: string) {
  window.open(url);
}
const pageData: IPageData = inject('pageData');
</script>

<template>
  <div>
    <div class="md:flex shadow">
      <div class="flex items-center">
        <div class="flex-grow md:hidden"></div>
        <img class="h-12 sm:h-20 ml-2 sm:ml-6 mr-3 block" :src="pageData.logoImageUrl" alt="">
        <div class="flex-grow md:hidden"></div>
      </div>
      <div class="hidden md:block md:flex-grow"></div>
      <button @click="openWindow(pageData.orgUrl)" class="nav-button h-20 hidden md:block">{{ pageData.orgUrlTitle[locale] }}</button>
      <button @click="openWindow(pageData.registrationUrl)" class="nav-button h-20 hidden md:block">{{ t('registration') }}</button>
      <button class="nav-button h-20 hidden md:block language">
        <span class="text-2xl material-icons">language</span>
        <div class="language-sub">
            <div @click="$i18n.locale='zh-TW'">中</div>
            <div @click="$i18n.locale='en-US'">EN</div>
        </div>
      </button>
      <div class="hidden md:block h-12 sm:h-20 w-2 sm:w-6"></div>
    </div>
    <div ref="target">
      <div class="h-8 block bg-gray-100 shadow-lg absolute top-12 sm:top-20 md:hidden left-0 w-full z-10 cursor-pointer text-center" @click="mobileNav = ! mobileNav">
        <span v-if="mobileNav==true" class="material-icons text-2xl text-gray-700">arrow_drop_up</span>
        <span v-if="mobileNav==false" class="material-icons text-2xl text-gray-700">arrow_drop_down</span>
      </div>
      <div :class="{'hidden': !mobileNav, 'block': mobileNav, 'md:block cursor-pointer': true}">
          <div :class="['md:flex shadow absolute md:bg-opacity-75 top-20 sm:top-28 md:top-20 left-0 w-full z-10', pageData.secondNavbarBackgroundColor]">
            <div class="hidden md:block md:flex-grow"></div>
            <button @click="$router.push(`/${$route.params.adminOrgId}/`)" class="nav-button button-height block">{{ t('homepage') }}</button>
            <button @click="$router.push(`/${$route.params.adminOrgId}/news`)" class="nav-button button-height block">{{ t('news') }}</button>
            <button @click="$router.push(`/${$route.params.adminOrgId}/games`)" class="nav-button button-height block">{{ t('game') }}</button>
            <button @click="$router.push(`/${$route.params.adminOrgId}/gallery`)" class="nav-button button-height block">{{ t('gallery') }}</button>
            <button @click="$router.push(`/${$route.params.adminOrgId}/venues`)" class="nav-button button-height block">{{ t('venue') }}</button>
            <button @click="$router.push(`/${$route.params.adminOrgId}/teams`)" class="nav-button button-height block">{{ t('team') }}</button>
            <button @click="openWindow(pageData.orgUrl)" class="nav-button button-height  block md:hidden">{{ pageData.orgUrlTitle[locale] }}</button>
            <button @click="openWindow(pageData.registrationUrl)" class="nav-button button-height  block md:hidden">{{ t('registration') }}</button>
            <button class="nav-button button-height  block md:hidden" @click="$i18n.locale='zh-TW'">中文</button>
            <button class="nav-button button-height  block md:hidden" @click="$i18n.locale='en-US'">English</button>
            <div class="hidden md:block md:flex-grow"></div>
          </div>
        </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.nav-button {
    @apply w-full md:w-auto px-5 text-xl font-medium text-gray-700 hover:bg-blue-500 hover:bg-opacity-95 hover:text-white duration-200;
}
.button-height {
    @apply h-12 md:h-16;
}
.language {
    @apply relative;
    .language-sub {
        @apply hidden absolute text-center top-20 left-0 w-16 bg-blue-500 z-50;
        div {
            @apply text-white p-2 hover:bg-blue-400 duration-200;
        }
    }
}
.language:hover {
    .language-sub {
        @apply block;
    }
}
</style>

<i18n lang="yaml">
en-US:
  homepage: 'Home'
  news: 'News'
  registration: 'Registration'
  game: 'Games'
  gallery: 'Gallery'
  team: 'Teams'
  venue: 'Venues'
zh-TW:
  homepage: '首頁'
  news: '最新消息'
  registration: '報名系統'
  game: '賽事列表'
  gallery: '影音資訊'
  team: '校隊資訊'
  venue: '場館資訊' 
</i18n>