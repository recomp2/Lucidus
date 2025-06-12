# Archetype Engine

The archetype engine provides character profiles that influence Lucidus responses.

## Concepts
- **Archetype**: a predefined set of traits that shape dialogue.
- **Mood Hooks**: actions like posting a scroll can adjust mood weights.

## Usage
1. Register archetypes in a custom plugin or theme.
2. Update the user's current archetype with `update_user_meta( $user_id, 'dbs_archetype', $slug );`.
3. Modify `lucidus_openai_chat()` to include archetype data when calling the OpenAI API.

## Extensibility
- Add custom mood logic by hooking into `dbs_update_mood` (create this action in your plugin).
- Store mood values in user meta for persistence.
