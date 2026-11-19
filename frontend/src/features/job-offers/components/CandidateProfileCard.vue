<script setup lang="ts">
import { computed } from "vue";
import type { JobOfferCandidate } from "../types/jobOffer.types";

const props = defineProps<{
    candidate: JobOfferCandidate;
}>();

const initials = computed(() => {
    return props.candidate.name
        .split(" ")
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part.charAt(0).toUpperCase())
        .join("");
});

const location = computed(() => {
    return (
        props.candidate.profile?.city ||
        props.candidate.profile?.address ||
        null
    );
});

const expectedSalary = computed(() => {
    const salary = props.candidate.profile?.expected_salary;

    if (salary === null || salary === undefined || salary === "") {
        return null;
    }

    const numericSalary = Number(salary);

    if (Number.isNaN(numericSalary)) {
        return String(salary);
    }

    return new Intl.NumberFormat("en-US", {
        maximumFractionDigits: 2,
    }).format(numericSalary);
});

const experienceYears = computed(() => {
    const years = props.candidate.profile?.experience_years;

    if (years === null || years === undefined || years === "") {
        return null;
    }

    const numericYears = Number(years);

    if (Number.isNaN(numericYears)) {
        return String(years);
    }

    return `${numericYears} ${numericYears === 1 ? "Year" : "Years"}`;
});

const hasProfileLinks = computed(() => {
    return Boolean(
        props.candidate.profile?.portfolio_url ||
        props.candidate.profile?.linkedin_url,
    );
});
</script>

<template>
    <aside class="space-y-4 lg:col-span-4 lg:sticky lg:top-24 self-start">
        <!-- Candidate Profile Card -->
        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
        >
            <div
                class="relative h-25 bg-linear-to-r from-indigo-600 via-indigo-500 to-violet-500"
            >
                <span
                    class="absolute right-3 top-3 inline-flex items-center gap-1.5 rounded-full border border-white/30 bg-white/95 px-2.5 py-1 text-xs font-semibold text-indigo-700 shadow-sm"
                >
                    Candidate Profile
                </span>
            </div>

            <div class="px-5 pb-5">
                <!-- Avatar -->
                <div
                    class="-mt-10 mb-3 flex items-end justify-between relative z-10"
                >
                    <img
                        v-if="candidate.profile?.profile_photo"
                        :src="candidate.profile.profile_photo"
                        :alt="candidate.name"
                        class="h-20 w-20 rounded-2xl border-4 border-white bg-slate-100 object-cover shadow-md"
                    />

                    <div
                        v-else
                        class="flex h-20 w-20 items-center justify-center rounded-2xl border-4 border-white bg-slate-900 text-2xl font-bold tracking-tight text-white shadow-md"
                    >
                        {{ initials }}
                    </div>
                </div>

                <!-- Name & Headline -->
                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        {{ candidate.name }}
                    </h2>

                    <p
                        v-if="candidate.profile?.professional_title"
                        class="text-sm font-medium text-indigo-600"
                    >
                        {{ candidate.profile.professional_title }}
                    </p>

                    <p v-else class="text-sm text-slate-500">
                        Professional title not provided
                    </p>
                </div>

                <!-- Metadata List -->
                <div
                    class="mt-4 space-y-2.5 border-t border-slate-100 pt-4 text-xs text-slate-600"
                >
                    <div v-if="location" class="flex items-center gap-2.5">
                        <svg
                            class="h-4 w-4 shrink-0 text-slate-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M17.657 16.657 13.414 20.9a2 2 0 0 1-2.828 0l-4.243-4.243a8 8 0 1 1 11.314 0Z"
                            />
                            <circle cx="12" cy="11" r="3" />
                        </svg>
                        <span>{{ location }}</span>
                    </div>
                    <div
                        v-if="experienceYears"
                        class="flex items-center gap-2.5"
                    >
                        <svg
                            class="h-4 w-4 shrink-0 text-slate-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <rect x="3" y="7" width="18" height="14" rx="2" />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18m-11 0v2h4v-2"
                            />
                        </svg>
                        <span>{{ experienceYears }} of Experience</span>
                    </div>
                    <div
                        v-if="expectedSalary"
                        class="flex items-center gap-2.5"
                    >
                        <svg
                            class="h-4 w-4 shrink-0 text-slate-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <circle cx="12" cy="12" r="9" />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 9.5c-.5-.7-1.4-1-3-1-1.4 0-2.5.7-2.5 1.7 0 2.6 5.5 1.1 5.5 3.7 0 1.1-1.1 1.8-2.8 1.8-1.4 0-2.5-.4-3.2-1.2M12 6.5v11"
                            />
                        </svg>
                        <span
                            >Expected salary:
                            <strong>{{ expectedSalary }}</strong></span
                        >
                    </div>
                    <div
                        v-if="candidate.email"
                        class="flex items-center gap-2.5"
                    >
                        <svg
                            class="h-4 w-4 shrink-0 text-slate-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <rect x="3" y="5" width="18" height="14" rx="2" />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m3 7 9 6 9-6"
                            />
                        </svg>
                        <span class="break-all">{{ candidate.email }}</span>
                    </div>
                    <div
                        v-if="candidate.profile?.phone"
                        class="flex items-center gap-2.5"
                    >
                        <svg
                            class="h-4 w-4 shrink-0 text-slate-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 3h4l2 5-3 2a14 14 0 0 0 6 6l2-3 5 2v4a2 2 0 0 1-2 2A17 17 0 0 1 3 5a2 2 0 0 1 2-2Z"
                            />
                        </svg>
                        <span>{{ candidate.profile.phone }}</span>
                    </div>
                </div>

                <!-- Bio -->
                <div
                    v-if="candidate.profile?.bio"
                    class="mt-4 border-t border-slate-100 pt-4"
                >
                    <p
                        class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400"
                    >
                        About Candidate
                    </p>

                    <p
                        class="whitespace-pre-line text-xs leading-relaxed text-slate-600"
                    >
                        {{ candidate.profile.bio }}
                    </p>
                </div>

                <!-- Skills -->
                <div class="mt-4 border-t border-slate-100 pt-4">
                    <p
                        class="mb-2.5 text-[11px] font-semibold uppercase tracking-wider text-slate-400"
                    >
                        Skills
                    </p>

                    <div
                        v-if="candidate.skills?.length"
                        class="flex flex-wrap gap-1.5"
                    >
                        <span
                            v-for="skill in candidate.skills"
                            :key="skill.id"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-100 bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700"
                        >
                            {{ skill.name }}

                            <!-- <span
                                v-if="skill.skill_level"
                                class="border-l border-indigo-200 pl-1.5 text-[10px] font-normal text-indigo-500"
                            >
                                {{ skill.skill_level }}
                            </span> -->
                        </span>
                    </div>

                    <p v-else class="text-xs text-slate-400">
                        No skills added yet.
                    </p>
                </div>

                <!-- Work Experience -->
                <!-- <div class="mt-4 border-t border-slate-100 pt-4">
                    <div class="mb-3 flex items-center justify-between">
                        <p
                            class="text-[11px] font-semibold uppercase tracking-wider text-slate-400"
                        >
                            Work Experience
                        </p>

                        <span class="text-xs text-slate-400">
                            {{ candidate?.experiences?.length }}
                        </span>
                    </div>

                    <div
                        v-if="candidate?.experiences?.length"
                        class="space-y-4"
                    >
                        <div
                            v-for="experience in candidate.experiences.slice(
                                0,
                                3,
                            )"
                            :key="experience.id"
                            class="relative border-l-2 border-indigo-100 pl-3"
                        >
                            <div
                                class="absolute -left-1.25 top-1 h-2 w-2 rounded-full bg-indigo-500"
                            />

                            <p class="text-xs font-semibold text-slate-800">
                                {{ experience.job_title }}
                            </p>

                            <p class="mt-0.5 text-xs text-slate-600">
                                {{ experience.company_name }}
                            </p>

                            <p class="mt-1 text-[11px] text-slate-400">
                                {{ experience.start_date }}
                                –
                                {{
                                    experience.is_current
                                        ? "Present"
                                        : experience.end_date || "Not specified"
                                }}
                            </p>

                            <span
                                v-if="experience.employment_type"
                                class="mt-1 inline-block rounded-md bg-slate-100 px-2 py-0.5 text-[10px] text-slate-500"
                            >
                                {{ experience.employment_type }}
                            </span>

                            <p
                                v-if="experience.description"
                                class="mt-1.5 line-clamp-3 text-xs leading-relaxed text-slate-500"
                            >
                                {{ experience.description }}
                            </p>
                        </div>
                    </div>

                    <p v-else class="text-xs text-slate-400">
                        No work experience added yet.
                    </p>
                </div> -->

                <!-- Portfolio & LinkedIn -->
                <div v-if="hasProfileLinks" class="mt-5 space-y-2">
                    <a
                        v-if="candidate.profile?.portfolio_url"
                        :href="candidate.profile.portfolio_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-100"
                    >
                        <svg
                            class="h-4 w-4 text-slate-500"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                            />
                        </svg>

                        View Portfolio
                    </a>

                    <a
                        v-if="candidate.profile?.linkedin_url"
                        :href="candidate.profile.linkedin_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-50"
                    >
                        <svg
                            class="h-4 w-4 text-slate-500"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-4 0v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"
                            />
                            <circle cx="4" cy="4" r="2" />
                        </svg>

                        View LinkedIn Profile
                    </a>
                </div>

                <div
                    v-else
                    class="mt-5 rounded-xl bg-slate-50 px-3 py-3 text-center text-xs text-slate-400"
                >
                    No public portfolio or LinkedIn link available.
                </div>
            </div>
        </div>

        <!-- Recruiter Tip Card -->
        <div
            class="flex items-start gap-3 rounded-2xl border border-indigo-100 bg-indigo-50/70 p-4"
        >
            <div class="shrink-0 rounded-xl bg-indigo-100 p-2 text-indigo-600">
                <svg
                    class="h-4 w-4"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M13 10V3L4 14h7v7l9-11h-7z"
                    />
                </svg>
            </div>

            <div class="text-xs leading-relaxed text-indigo-950">
                <p class="mb-0.5 font-semibold">Make Your Offer Relevant</p>

                <p class="text-indigo-800/80">
                    Highlight the candidate's relevant skills and explain how
                    the role matches their professional experience. Include
                    clear compensation details in your offer.
                </p>
            </div>
        </div>
    </aside>
</template>
