<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import Navbar from '@/components/Navbar.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/login';
import { Form, Head } from '@inertiajs/vue3';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Log in" />
    <div class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <Navbar />

        <main class="flex flex-1 items-center justify-center p-6 lg:p-8">
            <Card class="w-full max-w-md">
                <CardHeader class="text-center">
                    <CardTitle class="text-xl">Connexion</CardTitle>
                    <CardDescription>Tapez votre nom d'utilisateur et votre mot de passe</CardDescription>
                </CardHeader>

                <CardContent>
                    <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
                        {{ status }}
                    </div>

                    <Form v-bind="store.form()" :reset-on-success="['password']" v-slot="{ errors, processing }" class="flex flex-col gap-6">
                        <div class="grid gap-6">
                            <div class="grid gap-2">
                                <Label for="name">Nom d'utilisateur</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    name="name"
                                    required
                                    autofocus
                                    :tabindex="1"
                                    autocomplete="username"
                                    placeholder="Nom d'utilisateur"
                                />
                                <InputError :message="errors.name" />
                            </div>

                            <div class="grid gap-2">
                                <div class="flex items-center justify-between">
                                    <Label for="password">Mot de passe</Label>
                                </div>
                                <Input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    :tabindex="2"
                                    autocomplete="current-password"
                                    placeholder="Mot de passe"
                                />
                                <InputError :message="errors.password" />
                            </div>

                            <div class="flex items-center justify-between">
                                <Label for="remember" class="flex items-center space-x-3">
                                    <Checkbox id="remember" name="remember" :tabindex="3" />
                                    <span>Se souvenir de moi</span>
                                </Label>
                            </div>

                            <Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="processing" data-test="login-button">
                                <Spinner v-if="processing" />
                                Se connecter
                            </Button>
                        </div>
                    </Form>
                </CardContent>
            </Card>
        </main>
    </div>
</template>
